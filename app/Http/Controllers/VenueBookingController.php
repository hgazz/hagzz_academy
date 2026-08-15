<?php

namespace App\Http\Controllers;

use App\Models\PartnerUser;
use App\Models\VenueBooking;
use App\Models\VenueCustomer;
use App\Models\VenueSpace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VenueBookingController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    public function index()
    {
        $academyId = $this->getAcademyId();
        $bookings = VenueBooking::where('academy_id', $academyId)
            ->with(['space.venue', 'customer'])->orderByDesc('starts_at')->paginate(20);
        return view('Academy.pages.venue_bookings.index', compact('bookings'));
    }

    public function calendar()
    {
        $academyId = $this->getAcademyId();
        $bookings = VenueBooking::where('academy_id', $academyId)
            ->with(['space.venue', 'customer'])
            ->where('starts_at', '>=', now()->subMonths(3)->startOfMonth())
            ->where('starts_at', '<=', now()->addMonths(12)->endOfMonth())
            ->orderBy('starts_at')
            ->get();
        $spaces = VenueSpace::whereHas('venue', fn ($query) => $query->where('academy_id', $academyId))
            ->with('venue')->where('active', true)->get();
        $todayBookings = $bookings->filter(fn ($booking) => $booking->starts_at->isToday() && $booking->status !== 'cancelled');

        $events = $bookings->map(function ($booking) {
            $colors = [
                'pending' => '#d97706', 'confirmed' => '#2563eb', 'checked_in' => '#059669',
                'completed' => '#64748b', 'cancelled' => '#dc2626', 'no_show' => '#7c3aed',
            ];

            return [
                'id' => $booking->id,
                'title' => $booking->title ?: $booking->customer?->name ?: $booking->reference,
                'start' => $booking->starts_at->toIso8601String(),
                'end' => $booking->ends_at->toIso8601String(),
                'backgroundColor' => $colors[$booking->status] ?? '#2563eb',
                'borderColor' => $colors[$booking->status] ?? '#2563eb',
                'spaceId' => $booking->venue_space_id,
                'space' => $booking->space?->name,
                'venue' => $booking->space?->venue?->name,
                'customer' => $booking->customer?->name,
                'phone' => $booking->customer?->phone,
                'status' => $booking->status,
                'reference' => $booking->reference,
                'total' => (float) $booking->total_amount,
                'paid' => (float) $booking->paid_amount,
                'editUrl' => route('academy.venue-bookings.edit', $booking),
            ];
        })->values();

        $summary = [
            'today' => $todayBookings->count(),
            'confirmed' => $todayBookings->where('status', 'confirmed')->count(),
            'checkedIn' => $todayBookings->where('status', 'checked_in')->count(),
            'todayRevenue' => $todayBookings->sum('paid_amount'),
            'upcoming' => $bookings->filter(fn ($booking) => $booking->starts_at->isFuture() && !in_array($booking->status, ['cancelled', 'completed'], true))->count(),
        ];

        return view('Academy.pages.venue_bookings.calendar', compact('events', 'spaces', 'summary', 'todayBookings'));
    }

    public function create()
    {
        return view('Academy.pages.venue_bookings.form', $this->formData(new VenueBooking()));
    }

    public function store(Request $request)
    {
        $this->persist($request);
        return to_route('academy.venue-bookings.index')->with('success', trans('admin.venues.booking_saved') ?: 'تم حفظ الحجز بنجاح.');
    }

    public function edit(VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);
        return view('Academy.pages.venue_bookings.form', $this->formData($venueBooking));
    }

    public function update(Request $request, VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);
        $this->persist($request, $venueBooking);
        return to_route('academy.venue-bookings.index')->with('success', trans('admin.venues.booking_saved') ?: 'تم تحديث بيانات الحجز بنجاح.');
    }

    public function collectPayment(Request $request, VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);

        $remaining = $venueBooking->remaining_amount;
        if ($remaining <= 0) {
            return back()->with('info', trans('admin.venues.already_fully_paid') ?: 'هذا الحجز مسدد بالكامل بالفعل.');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $remaining],
            'payment_method' => ['required', 'in:cash,card,instapay,fawry,bank_transfer,sadad,stc_pay,apple_pay,other'],
            'payment_method_other' => ['required_if:payment_method,other', 'nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $newPaid = (float) $venueBooking->paid_amount + (float) $data['amount'];
        $notes = $venueBooking->notes;
        if (!empty($data['notes'])) {
            $notes = trim(($notes ? $notes . ' | ' : '') . 'دفعة محصلة: ' . number_format($data['amount'], 2) . ' عبر ' . $data['payment_method'] . ' (' . $data['notes'] . ')');
        }

        $venueBooking->update([
            'paid_amount' => $newPaid,
            'payment_method' => $data['payment_method'],
            'payment_method_other' => $data['payment_method_other'] ?? null,
            'notes' => $notes,
        ]);

        return back()->with('success', trans('admin.venues.payment_collected') ?: 'تم تسجيل تحصيل الدفعة بنجاح وتحديث الفاتورة.');
    }

    public function applyDiscount(Request $request, VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);

        $maxDiscount = (float) $venueBooking->total_amount - (float) $venueBooking->paid_amount;
        $data = $request->validate([
            'discount_amount' => ['required', 'numeric', 'min:0.01', 'max:' . max(0.01, $maxDiscount)],
            'discount_reason' => ['required', 'string', 'max:255'],
            'discount_approved_by' => ['nullable', 'string', 'max:255'],
        ]);

        $approver = $data['discount_approved_by'] ?: (auth('academy')->user()?->name ?: 'الإدارة');

        $notes = $venueBooking->notes;
        $noteEntry = 'خصم معتمد بقيمة: ' . number_format($data['discount_amount'], 2) . ' ج.م (السبب: ' . $data['discount_reason'] . ' - اعتماد: ' . $approver . ')';
        $notes = trim(($notes ? $notes . ' | ' : '') . $noteEntry);

        $venueBooking->update([
            'discount_amount' => $data['discount_amount'],
            'discount_reason' => $data['discount_reason'],
            'discount_approved_by' => $approver,
            'discount_approved_at' => now(),
            'notes' => $notes,
        ]);

        return back()->with('success', 'تم اعتماد وتطبيق الخصم بنجاح وتحديث الفاتورة والمتبقي.');
    }

    public function removeDiscount(Request $request, VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);

        if ((float) $venueBooking->discount_amount <= 0) {
            return back()->with('info', 'لا يوجد خصم مسجل على هذا الحجز.');
        }

        $prevDiscount = number_format((float) $venueBooking->discount_amount, 2);
        $reverser = auth('academy')->user()?->name ?: 'الإدارة';
        $notes = $venueBooking->notes;
        $notes = trim(($notes ? $notes . ' | ' : '') . 'تم استرداد وإلغاء خصم سابق بقيمة: ' . $prevDiscount . ' ج.م بواسطة: ' . $reverser);

        $venueBooking->update([
            'discount_amount' => 0,
            'discount_reason' => null,
            'discount_approved_by' => null,
            'discount_approved_at' => null,
            'notes' => $notes,
        ]);

        return back()->with('success', 'تم إلغاء واسترداد الخصم وإعادة المبلغ لرصيد المتبقي.');
    }

    public function destroy(VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);
        $venueBooking->update(['status' => 'cancelled']);
        return back()->with('success', trans('admin.venues.booking_cancelled') ?: 'تم إلغاء الحجز بنجاح.');
    }

    private function persist(Request $request, ?VenueBooking $booking = null): VenueBooking
    {
        $academyId = $this->getAcademyId();
        $data = $request->validate([
            'venue_space_id' => ['required', 'integer'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'booking_type' => ['required', 'in:individual,tournament,event'],
            'title' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:pending,confirmed,checked_in,completed,cancelled,no_show'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,instapay,fawry,app_online,bank_transfer,card,other'],
            'payment_method_other' => ['required_if:payment_method,other', 'nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data, $booking, $academyId) {
            $space = VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', $academyId))->lockForUpdate()->findOrFail($data['venue_space_id']);
            $startsAt = Carbon::parse($data['date'].' '.$data['start_time']);
            $endsAt = Carbon::parse($data['date'].' '.$data['end_time']);
            if ($endsAt->lessThanOrEqualTo($startsAt)) {
                $endsAt->addDay();
            }
            $openMinutes = ((int) substr($space->opens_at, 0, 2) * 60) + (int) substr($space->opens_at, 3, 2);
            $closeMinutes = ((int) substr($space->closes_at, 0, 2) * 60) + (int) substr($space->closes_at, 3, 2);
            if ($closeMinutes <= $openMinutes) {
                $closeMinutes += 1440;
            }
            $bookingStartMinutes = ((int) $startsAt->format('H') * 60) + (int) $startsAt->format('i');
            $durationMinutes = (int) $startsAt->diffInMinutes($endsAt);
            if ($bookingStartMinutes < $openMinutes || ($bookingStartMinutes + $durationMinutes) > $closeMinutes) {
                throw ValidationException::withMessages(['start_time' => trans('admin.venues.outside_hours') ?: 'وقت الحجز يقع خارج أوقات العمل المحددة للملعب/المساحة.']);
            }
            if ($durationMinutes < $space->slot_minutes || $durationMinutes % $space->slot_minutes !== 0) {
                throw ValidationException::withMessages(['end_time' => trans('admin.venues.invalid_slot', ['minutes' => $space->slot_minutes]) ?: "مدة الحجز يجب أن تكون من مضاعفات {$space->slot_minutes} دقيقة."]);
            }
            $overlap = VenueBooking::where('venue_space_id', $space->id)->where('id', '!=', $booking?->id ?? 0)
                ->whereNotIn('status', ['cancelled'])->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt)->exists();
            if ($overlap) {
                throw ValidationException::withMessages(['start_time' => trans('admin.venues.overlap') ?: 'يوجد حجز آخر متعارض في هذا الوقت.']);
            }

            $total = round(($durationMinutes / 60) * (float) $space->hourly_price, 2);
            if ((float) $data['paid_amount'] > $total) {
                throw ValidationException::withMessages(['paid_amount' => trans('admin.bookings.paid_amount_exceeds_total') ?: 'المبلغ المدفوع لا يمكن أن يتجاوز إجمالي قيمة الحجز.']);
            }
            $customer = VenueCustomer::updateOrCreate(
                ['academy_id' => $academyId, 'phone' => $data['customer_phone']],
                ['name' => $data['customer_name'], 'email' => $data['customer_email'] ?? null]
            );
            $values = [
                'academy_id' => $academyId,
                'venue_space_id' => $space->id,
                'venue_customer_id' => $customer->id,
                'booking_type' => $data['booking_type'],
                'title' => $data['title'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $data['status'],
                'total_amount' => $total,
                'paid_amount' => $data['paid_amount'],
                'payment_method' => $data['payment_method'],
                'payment_method_other' => $data['payment_method_other'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];
            if ($booking) {
                $booking->update($values);
                return $booking;
            }
            return VenueBooking::create($values + ['reference' => 'V-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -6))]);
        });
    }

    private function formData(VenueBooking $booking): array
    {
        $academyId = $this->getAcademyId();
        return [
            'venueBooking' => $booking,
            'spaces' => VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', $academyId))->with('venue')->where('active', true)->get(),
        ];
    }

    private function authorizeTenant(VenueBooking $booking): void
    {
        abort_unless($booking->academy_id === $this->getAcademyId(), 404);
    }
}
