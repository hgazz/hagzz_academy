<?php

namespace App\Exports;

use App\Models\Settlement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class SettlementExport implements FromView
{
    protected  $data;
    public function __construct()
    {
        $settlementData = session('settlementData', []);
        $settlements = Settlement::where('partner_id',auth('academy')->id())->get();
        if (!empty($settlementData) && count($settlementData) > 0){
            $this->data = $settlementData;
        }else{
            $this->data = $settlements;
        }
    }


    public function view(): View
    {
        return  view('Academy.pages.settlements.export',with(['settlements'=>$this->data]));
    }
}
