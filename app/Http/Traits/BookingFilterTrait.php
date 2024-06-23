<?php

namespace App\Http\Traits;

use App\Models\Join;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait BookingFilterTrait
{
    private function getTotalBookingBalance($startDate, $endDate)
    {
        return $this->buildDateFilteredQuery($startDate, $endDate)->sum('price');
    }

    private function getTotalBookingRefundCount($startDate, $endDate): int
    {
        return $this->buildDateFilteredQuery($startDate, $endDate)
            ->whereHas('invoice', function ($query) {
                $query->where('is_canceled', 1);
            })->count();
    }

    private function getTotalBookingRefundAmount($startDate, $endDate)
    {
        return $this->buildDateFilteredQuery($startDate, $endDate)
            ->whereHas('invoice', function ($query) {
                $query->where('is_canceled', 1);
            })->sum('price');
    }

    private function getTotalBookingCount($startDate, $endDate): int
    {
        return $this->buildDateFilteredQuery($startDate, $endDate)->count();
    }

    private function buildDateFilteredQuery($startDate, $endDate): Builder
    {
        $query = Join::query();
        if ($startDate && $endDate) {
            $query->whereHas('training', function ($q) {
                $q->where('academy_id', auth('academy')->id());
            })->whereBetween('created_at', [$startDate, Carbon::create($endDate)->endOfDay()]);
        }
        return $query;
    }
}
