<?php

namespace Tests\Unit;

use App\Models\VenueBooking;
use PHPUnit\Framework\TestCase;

class VenueBookingPaymentTest extends TestCase
{
    public function test_partial_payment_has_remaining_balance(): void
    {
        $booking = new VenueBooking(['total_amount' => 600, 'paid_amount' => 200]);
        $this->assertSame(400.0, $booking->remaining_amount);
        $this->assertSame('partial', $booking->payment_status);
    }

    public function test_full_payment_has_no_remaining_balance(): void
    {
        $booking = new VenueBooking(['total_amount' => 600, 'paid_amount' => 600]);
        $this->assertSame(0.0, $booking->remaining_amount);
        $this->assertSame('paid', $booking->payment_status);
    }
}
