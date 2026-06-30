<?php

namespace App\Http\Controllers;

use App\Models\VenueBooking;
use App\Models\VenueCustomer;
use App\Models\VenueSpace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VenueBookingController extends Controller
{
    public function index()
    {
        $bookings = VenueBooking::where('academy_id', auth('academy')->id())
            ->with(['space.venue', 'customer'])->orderByDesc('starts_at')->paginate(20);
        return view('Academy.pages.venue_bookings.index', compact('bookings'));
    }

    public function create() { return view('Academy.pages.venue_bookings.form', $this->formData(new VenueBooking())); }

    public function store(Request $request)
    {
        $this->persist($request);
        return to_route('academy.venue-bookings.index')->with('success', trans('admin.venues.booking_saved'));
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
        return to_route('academy.venue-bookings.index')->with('success', trans('admin.venues.booking_saved'));
    }

    public function destroy(VenueBooking $venueBooking)
    {
        $this->authorizeTenant($venueBooking);
        $venueBooking->update(['status' => 'cancelled']);
        return back()->with('success', trans('admin.venues.booking_cancelled'));
    }

    private function persist(Request $request, ?VenueBooking $booking = null): VenueBooking
    {
        $data = $request->validate([
            'venue_space_id' => ['required', 'integer'], 'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'], 'customer_email' => ['nullable', 'email', 'max:255'],
            'booking_type' => ['required', 'in:individual,tournament,event'], 'title' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:pending,confirmed,checked_in,completed,cancelled,no_show'],
            'paid_amount' => ['required', 'numeric', 'min:0'], 'payment_method' => ['required', 'in:cash,instapay,fawry,app_online,bank_transfer,card,other'],
            'payment_method_other' => ['required_if:payment_method,other', 'nullable', 'string', 'max:255'], 'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data, $booking) {
            $space = VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', auth('academy')->id()))->lockForUpdate()->findOrFail($data['venue_space_id']);
            $startsAt = Carbon::parse($data['date'].' '.$data['start_time']);
            $endsAt = Carbon::parse($data['date'].' '.$data['end_time']);
            if ($endsAt->lessThanOrEqualTo($startsAt)) $endsAt->addDay();
            $openMinutes = ((int) substr($space->opens_at, 0, 2) * 60) + (int) substr($space->opens_at, 3, 2);
            $closeMinutes = ((int) substr($space->closes_at, 0, 2) * 60) + (int) substr($space->closes_at, 3, 2);
            if ($closeMinutes <= $openMinutes) $closeMinutes += 1440;
            $bookingStartMinutes = ((int) $startsAt->format('H') * 60) + (int) $startsAt->format('i');
            $durationMinutes = (int) $startsAt->diffInMinutes($endsAt);
            if ($bookingStartMinutes < $openMinutes || ($bookingStartMinutes + $durationMinutes) > $closeMinutes) {
                throw ValidationException::withMessages(['start_time' => trans('admin.venues.outside_hours')]);
            }
            if ($durationMinutes < $space->slot_minutes || $durationMinutes % $space->slot_minutes !== 0) {
                throw ValidationException::withMessages(['end_time' => trans('admin.venues.invalid_slot', ['minutes' => $space->slot_minutes])]);
            }
            $overlap = VenueBooking::where('venue_space_id', $space->id)->where('id', '!=', $booking?->id ?? 0)
                ->whereNotIn('status', ['cancelled'])->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt)->exists();
            if ($overlap) throw ValidationException::withMessages(['start_time' => trans('admin.venues.overlap')]);

            $total = round(($durationMinutes / 60) * (float) $space->hourly_price, 2);
            if ((float) $data['paid_amount'] > $total) throw ValidationException::withMessages(['paid_amount' => trans('admin.bookings.paid_amount_exceeds_total')]);
            $customer = VenueCustomer::updateOrCreate(
                ['academy_id' => auth('academy')->id(), 'phone' => $data['customer_phone']],
                ['name' => $data['customer_name'], 'email' => $data['customer_email'] ?? null]
            );
            $values = [
                'academy_id' => auth('academy')->id(), 'venue_space_id' => $space->id, 'venue_customer_id' => $customer->id,
                'booking_type' => $data['booking_type'], 'title' => $data['title'] ?? null, 'starts_at' => $startsAt, 'ends_at' => $endsAt,
                'status' => $data['status'], 'total_amount' => $total, 'paid_amount' => $data['paid_amount'],
                'payment_method' => $data['payment_method'], 'payment_method_other' => $data['payment_method_other'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];
            if ($booking) { $booking->update($values); return $booking; }
            return VenueBooking::create($values + ['reference' => 'V-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -6))]);
        });
    }

    private function formData(VenueBooking $booking): array
    {
        return ['venueBooking' => $booking, 'spaces' => VenueSpace::whereHas('venue', fn ($q) => $q->where('academy_id', auth('academy')->id()))->with('venue')->where('active', true)->get()];
    }
    private function authorizeTenant(VenueBooking $booking): void { abort_unless($booking->academy_id === auth('academy')->id(), 404); }
}
