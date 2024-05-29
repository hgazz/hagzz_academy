<?php

namespace App\Exports;

use App\Models\Address;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AddressExport implements FromView
{

    public function view(): View
    {
        $addresses = Address::with(['academy','country','city','area'])->where('academy_id', auth('academy')->id())->get();
        return view('Academy.pages.address.export',compact('addresses'));
    }
}
