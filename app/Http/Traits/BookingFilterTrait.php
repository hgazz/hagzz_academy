<?php

namespace App\Http\Traits;

use App\Models\Join;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait BookingFilterTrait
{
    private function getTotalBookingBalance($startDate, $endDate, $academyId)
    {
        return $this->buildDateFilteredQuery($startDate, $endDate, $academyId)->sum('price');
    }

    private function getTotalBookingRefundCount($startDate, $endDate, $academyId): int
    {
        return $this->buildDateFilteredQuery($startDate, $endDate, $academyId)
            ->whereHas('invoice', function ($query) {
                $query->where('is_canceled', 1);
            })->count();
    }

    private function getTotalBookingRefundAmount($startDate, $endDate, $academyId)
    {
        return $this->buildDateFilteredQuery($startDate, $endDate, $academyId)
            ->whereHas('invoice', function ($query) {
                $query->where('is_canceled', 1);
            })->sum('price');
    }

    private function getTotalBookingCount($startDate, $endDate, $academyId): int
    {
        return $this->buildDateFilteredQuery($startDate, $endDate, $academyId)->count();
    }

    private function buildDateFilteredQuery($startDate, $endDate, $academyId): Builder
    {
        $query = Join::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, Carbon::create($endDate)->endOfDay()]);
        }
        return $query->whereHas('training', function ($q) use ($academyId) {
            $q->where('academy_id', $academyId);
        });
    }
}
