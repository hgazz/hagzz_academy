<?php

namespace Tests\Unit;

use App\Models\Invoice;
use PHPUnit\Framework\TestCase;

class InvoicePaymentTest extends TestCase
{
    public function test_legacy_invoice_is_treated_as_fully_paid(): void
    {
        $invoice = new Invoice(['amount' => 250]);

        $this->assertSame(250.0, $invoice->collected_amount);
        $this->assertSame(0.0, $invoice->remaining_amount);
        $this->assertSame('paid', $invoice->payment_state);
    }

    public function test_partial_payment_calculates_the_remaining_amount(): void
    {
        $invoice = new Invoice(['amount' => 250, 'paid_amount' => 100]);

        $this->assertSame(100.0, $invoice->collected_amount);
        $this->assertSame(150.0, $invoice->remaining_amount);
        $this->assertSame('partial', $invoice->payment_state);
    }

    public function test_zero_payment_is_unpaid(): void
    {
        $invoice = new Invoice(['amount' => 250, 'paid_amount' => 0]);

        $this->assertSame(250.0, $invoice->remaining_amount);
        $this->assertSame('unpaid', $invoice->payment_state);
    }
}
