<?php

namespace App\Http\Controllers;

use App\Models\Academies;
use App\Models\AcademyStudentPayment;
use App\Models\AcademyStudentSubscription;
use App\Models\Invoice;
use App\Models\PartnerShiftClosing;
use App\Models\PartnerUser;
use App\Models\VenueBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartnerShiftClosingController extends Controller
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
        $closings = PartnerShiftClosing::where('academy_id', $academyId)
            ->latest('closed_at')
            ->paginate(15);

        return view('Academy.pages.shift_closings.index', compact('closings'));
    }

    public function create(Request $request)
    {
        $academyId = $this->getAcademyId();

        // Find the last shift closed_at timestamp or default to start of today
        $lastShift = PartnerShiftClosing::where('academy_id', $academyId)->latest('closed_at')->first();
        $startedAt = $lastShift ? $lastShift->closed_at : now()->startOfDay();
        $closedAt = now();

        $metrics = $this->calculateShiftMetrics($academyId, $startedAt, $closedAt);

        return view('Academy.pages.shift_closings.create', compact('startedAt', 'closedAt', 'metrics', 'lastShift'));
    }

    public function store(Request $request)
    {
        $academyId = $this->getAcademyId();
        $authUser = auth('academy')->user();

        $data = $request->validate([
            'shift_title' => ['required', 'string', 'max:100'],
            'started_at' => ['required', 'date'],
            'closed_at' => ['required', 'date', 'after_or_equal:started_at'],
            'actual_cash_counted' => ['required', 'numeric', 'min:0'],
            'next_shift_receiver' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $startedAt = Carbon::parse($data['started_at']);
        $closedAt = Carbon::parse($data['closed_at']);

        $metrics = $this->calculateShiftMetrics($academyId, $startedAt, $closedAt);

        $actualCash = (float) $data['actual_cash_counted'];
        $cashDiff = round($actualCash - $metrics['cash'], 2);

        $closing = PartnerShiftClosing::create([
            'academy_id' => $academyId,
            'partner_user_id' => $authUser instanceof PartnerUser ? $authUser->id : null,
            'closed_by_name' => $authUser?->name ?: 'الإدارة',
            'shift_title' => $data['shift_title'],
            'started_at' => $startedAt,
            'closed_at' => $closedAt,
            'total_cash_system' => $metrics['cash'],
            'total_card_system' => $metrics['card'],
            'total_instapay_system' => $metrics['instapay'],
            'total_fawry_system' => $metrics['fawry'],
            'total_bank_system' => $metrics['bank_transfer'],
            'total_other_system' => $metrics['other'],
            'total_discounts_system' => $metrics['discounts'],
            'total_collected_system' => $metrics['total_collected'],
            'actual_cash_counted' => $actualCash,
            'cash_difference' => $cashDiff,
            'next_shift_receiver' => $data['next_shift_receiver'],
            'notes' => $data['notes'],
            'status' => 'closed',
        ]);

        return to_route('academy.shift-closings.show', $closing)->with('success', 'تم تقفيل الوردية اليومية بنجاح وحفظ تقرير Z-Report.');
    }

    public function show(PartnerShiftClosing $shiftClosing)
    {
        abort_unless((int) $shiftClosing->academy_id === $this->getAcademyId(), 404);

        $academy = Academies::find($this->getAcademyId());
        return view('Academy.pages.shift_closings.show', compact('shiftClosing', 'academy'));
    }

    private function calculateShiftMetrics(int $academyId, Carbon $startedAt, Carbon $closedAt): array
    {
        $cash = 0; $card = 0; $instapay = 0; $fawry = 0; $bank = 0; $other = 0; $discounts = 0;

        // 1. Venue Bookings in this time window
        $venueBookings = VenueBooking::where('academy_id', $academyId)
            ->whereBetween('updated_at', [$startedAt, $closedAt])
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($venueBookings as $vb) {
            $amt = (float) $vb->paid_amount;
            $method = strtolower((string) $vb->payment_method);
            if ($amt > 0) {
                if (in_array($method, ['cash', 'نقداً', 'كاش'])) $cash += $amt;
                elseif (in_array($method, ['card', 'pos', 'visa', 'mastercard', 'بطاقة'])) $card += $amt;
                elseif (in_array($method, ['instapay', 'إنستاباي'])) $instapay += $amt;
                elseif (in_array($method, ['fawry', 'فوري'])) $fawry += $amt;
                elseif (in_array($method, ['bank_transfer', 'تحويل بنكي'])) $bank += $amt;
                else $other += $amt;
            }
            $discounts += (float) ($vb->discount_amount ?? 0);
        }

        // 2. Student Subscription Payments in this time window
        $studentPayments = AcademyStudentPayment::whereHas('subscription.student', fn ($q) => $q->where('academy_id', $academyId))
            ->whereBetween('paid_at', [$startedAt->toDateString(), $closedAt->toDateString()])
            ->get();

        foreach ($studentPayments as $sp) {
            $amt = (float) $sp->amount;
            $method = strtolower((string) $sp->method);
            if ($amt > 0) {
                if ($method === 'cash') $cash += $amt;
                elseif (in_array($method, ['card', 'app_online'])) $card += $amt;
                elseif ($method === 'instapay') $instapay += $amt;
                elseif ($method === 'fawry') $fawry += $amt;
                elseif ($method === 'bank_transfer') $bank += $amt;
                else $other += $amt;
            }
        }

        // Student subscription discounts
        $studentDiscounts = AcademyStudentSubscription::whereHas('student', fn ($q) => $q->where('academy_id', $academyId))
            ->whereBetween('discount_approved_at', [$startedAt, $closedAt])
            ->sum('discount_amount');
        $discounts += (float) $studentDiscounts;

        // 3. Training Booking Invoices
        $trainingInvoices = Invoice::whereHas('training', fn ($q) => $q->where('academy_id', $academyId))
            ->whereBetween('updated_at', [$startedAt, $closedAt])
            ->where('is_canceled', false)
            ->get();

        foreach ($trainingInvoices as $inv) {
            $amt = (float) ($inv->collected_amount ?? $inv->paid_amount ?? 0);
            $method = strtolower((string) $inv->payment_method);
            if ($amt > 0) {
                if (in_array($method, ['cash', 'كاش', '1'])) $cash += $amt;
                elseif (in_array($method, ['card', 'visa', '2', 'online'])) $card += $amt;
                elseif (in_array($method, ['instapay'])) $instapay += $amt;
                elseif (in_array($method, ['fawry'])) $fawry += $amt;
                else $other += $amt;
            }
        }

        $totalCollected = $cash + $card + $instapay + $fawry + $bank + $other;

        return [
            'cash' => round($cash, 2),
            'card' => round($card, 2),
            'instapay' => round($instapay, 2),
            'fawry' => round($fawry, 2),
            'bank_transfer' => round($bank, 2),
            'other' => round($other, 2),
            'discounts' => round($discounts, 2),
            'total_collected' => round($totalCollected, 2),
        ];
    }
}
