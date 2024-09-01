<?php

namespace App\Services\Chart;

use App\Models\Invoice;
use App\Models\Join;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChartsService
{
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    public function getBookingsDataByMonth($query = null)
    {
        $joins = $this->getGroupedByMonth(Join::class,[
            "(COUNT(*)) as count",
            "MONTHNAME(created_at) as monthname",
            "sum(price) as total"
        ],$query);

        return [
            'joinsData' => $this->getDataForEachMonth($joins),
            'total' => $joins->sum('total')
        ];
    }
    public function getOrderReturnsDataByMonth($query = null)
    {

        if ($query == null) {
            $query = Invoice::query();
        }

        // Add condition to include only records where is_canceled is 1
        $query->whereHas('training',function ($q){
            $q->where('academy_id', auth('academy')->id());
        })->where('is_canceled', 1);

        $orderReturns = $this->getGroupedByMonth(
            Invoice::class,
            [
                "(COUNT(*)) as count",
                "MONTHNAME(created_at) as monthname",
                "SUM(amount) as total"
            ],
            $query
        );

        return [
            'orderReturnsData' => $this->getDataForEachMonth($orderReturns),
            'total' => $orderReturns->sum('total')
        ];
    }


    private function getDataForEachMonth(Collection $collection)
    {
        $data = [];

        foreach(self::MONTHS as $month)
        {
            $data []= $collection->where('monthname',$month)->first()->count ?? 0;
        }
        return $data;
    }

    private function getGroupedByMonth($model,array $selects,$query = null)
    {
        if($query == null)
        {
            $query = $model::query();
        }
        $query->whereHas('training',function ($q){
            $q->where('academy_id', auth('academy')->id());
        });

        $array = [];
        foreach($selects as $select)
        {
            $array []= DB::raw($select);
        }
        $query->select(...$array);
        return $query->groupBy('monthname')
            ->get()
            ->sortBy(function($item){
                return (Carbon::parse($item->monthname)->format('m'));
            });
    }
}
