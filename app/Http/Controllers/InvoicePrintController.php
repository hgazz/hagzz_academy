<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudentSubscription;
use App\Models\Invoice;
use App\Models\TenantSubscriptionInvoice;
use App\Models\VenueBooking;
use Illuminate\Http\Request;

class InvoicePrintController extends Controller
{
    public function index()
    {
        $invoices = TenantSubscriptionInvoice::query()
            ->with('subscription.plan')
            ->where('academy_id', auth('academy')->id())
            ->latest('issued_at')
            ->paginate(20);

        return view('Academy.pages.billing_invoices.index', compact('invoices'));
    }

    public function booking(Request $request, Invoice $invoice)
    {
        $invoice->loadMissing(['user', 'training.academy']);
        $this->owns($invoice->training?->academy_id);
        $academy = $invoice->training->academy;
        $total = (float) $invoice->amount;
        $paid = $invoice->collected_amount;

        return $this->render($request, [
            'type' => 'booking', 'number' => $invoice->order_number ?: 'BK-' . $invoice->id,
            'issued_at' => $invoice->created_at, 'seller' => $this->academyParty($academy),
            'buyer' => $this->party($invoice->user?->name, $invoice->user?->phone, $invoice->user?->email),
            'lines' => [['description' => $invoice->training?->name ?: 'Training booking', 'quantity' => 1, 'unit_price' => $total, 'total' => $total]],
            'subtotal' => $total, 'discount' => 0, 'tax' => 0, 'total' => $total, 'paid' => $paid,
            'balance' => max(0, $total - $paid), 'currency' => 'EGP',
            'status' => $invoice->is_canceled ? 'cancelled' : $invoice->payment_state,
            'payment_method' => $invoice->payment_method_label,
        ]);
    }

    public function student(Request $request, AcademyStudentSubscription $subscription)
    {
        $subscription->loadMissing(['student.academy', 'group', 'payments']);
        $this->owns($subscription->student?->academy_id);
        $total = (float) $subscription->amount;

        return $this->render($request, [
            'type' => 'student_subscription', 'number' => 'STU-' . str_pad((string) $subscription->id, 6, '0', STR_PAD_LEFT),
            'issued_at' => $subscription->created_at, 'due_at' => $subscription->starts_on,
            'seller' => $this->academyParty($subscription->student->academy),
            'buyer' => $this->party($subscription->student?->name, $subscription->student?->phone, $subscription->student?->email, null, $subscription->student?->guardian_name),
            'lines' => [['description' => trim(($subscription->group?->name ?: 'Student subscription') . ' · ' . optional($subscription->starts_on)->format('Y-m-d') . ' — ' . optional($subscription->ends_on)->format('Y-m-d')), 'quantity' => 1, 'unit_price' => $total, 'total' => $total]],
            'subtotal' => $total, 'discount' => 0, 'tax' => 0, 'total' => $total,
            'paid' => $subscription->paid_amount, 'balance' => $subscription->remaining_amount, 'currency' => 'EGP',
            'status' => $subscription->status === 'cancelled' ? 'cancelled' : $subscription->payment_status,
            'payment_method' => $subscription->payments->sortByDesc('paid_at')->first()?->method_label,
            'notes' => $subscription->notes,
        ]);
    }

    public function venue(Request $request, VenueBooking $booking)
    {
        $booking->loadMissing(['customer', 'space.venue.academy']);
        $this->owns($booking->academy_id);
        $total = (float) $booking->total_amount;
        $description = trim(($booking->space?->venue?->name ?? '') . ' - ' . ($booking->space?->name ?? ''), ' -');

        return $this->render($request, [
            'type' => 'venue_booking', 'number' => $booking->reference ?: 'VEN-' . $booking->id,
            'issued_at' => $booking->created_at, 'seller' => $this->academyParty($booking->space?->venue?->academy),
            'buyer' => $this->party($booking->customer?->name, $booking->customer?->phone, $booking->customer?->email),
            'lines' => [['description' => $description . ' · ' . optional($booking->starts_at)->format('Y-m-d H:i') . ' — ' . optional($booking->ends_at)->format('H:i'), 'quantity' => 1, 'unit_price' => $total, 'total' => $total]],
            'subtotal' => $total, 'discount' => 0, 'tax' => 0, 'total' => $total,
            'paid' => (float) $booking->paid_amount, 'balance' => $booking->remaining_amount, 'currency' => 'EGP',
            'status' => $booking->status === 'cancelled' ? 'cancelled' : $booking->payment_status,
            'payment_method' => $booking->payment_method, 'notes' => $booking->notes,
        ]);
    }

    public function platform(Request $request, TenantSubscriptionInvoice $invoice)
    {
        $invoice->loadMissing(['academy', 'subscription.plan', 'payments']);
        $this->owns($invoice->academy_id);
        $plan = $invoice->subscription?->plan?->name;

        return $this->render($request, [
            'type' => 'platform_subscription', 'number' => $invoice->invoice_number,
            'issued_at' => $invoice->issued_at, 'due_at' => $invoice->due_at,
            'seller' => $this->party('Hagzz', null, null, null, null, asset('assetsAdmin/logo/Primary.svg')), 'buyer' => $this->academyParty($invoice->academy),
            'lines' => [['description' => trim(($plan ?: 'Hagzz platform subscription') . ' · ' . optional($invoice->period_starts_at)->format('Y-m-d') . ' — ' . optional($invoice->period_ends_at)->format('Y-m-d')), 'quantity' => 1, 'unit_price' => (float) $invoice->list_amount, 'total' => (float) $invoice->subtotal_amount]],
            'subtotal' => (float) $invoice->list_amount, 'discount' => (float) $invoice->discount_amount,
            'tax' => (float) $invoice->tax_amount, 'tax_rate' => (float) $invoice->tax_rate,
            'total' => (float) $invoice->total_amount, 'paid' => (float) $invoice->paid_amount,
            'balance' => $invoice->balance, 'currency' => $invoice->currency_code, 'status' => $invoice->status,
            'payment_method' => $invoice->payments->sortByDesc('paid_at')->first()?->payment_method, 'notes' => $invoice->notes,
        ]);
    }

    private function render(Request $request, array $document)
    {
        $paper = $request->validate(['paper' => ['nullable', 'in:a4,a5,pos']])['paper'] ?? 'a4';
        $signaturePayload = implode('|', [$document['type'], $document['number'], $document['total'], optional($document['issued_at'] ?? null)->format('c')]);
        $document['printed_at'] = now();
        $document['signature_reference'] = strtoupper(substr(hash_hmac('sha256', $signaturePayload, (string) config('app.key')), 0, 20));
        $document['platform_logo'] = asset('assetsAdmin/logo/Primary.svg');
        return view('Academy.pages.invoice_print.show', compact('document', 'paper'));
    }

    private function owns($academyId): void { abort_unless((int) $academyId === (int) auth('academy')->id(), 404); }
    private function academyParty($academy): array { return $this->party($academy?->commercial_name, $academy?->phone, $academy?->email, $academy?->tax_number, $academy?->address, $academy?->logo); }
    private function party($name, $phone = null, $email = null, $taxNumber = null, $address = null, $logo = null): array { return compact('name', 'phone', 'email', 'taxNumber', 'address', 'logo'); }
}
