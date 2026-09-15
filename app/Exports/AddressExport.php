<?php

namespace App\Exports;

use App\Models\Address;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AddressExport implements FromView
{

    public function view(): View
    {
        $user = auth('academy')->user();
        $academyId = $user instanceof \App\Models\PartnerUser ? (int) $user->academy_id : (int) auth('academy')->id();
        $query = Address::with(['academy', 'country', 'city', 'area'])
            ->where('academy_id', $academyId);
        $addresses = $query->get();
        return view('Academy.pages.address.export', compact('addresses'));
    }
}
