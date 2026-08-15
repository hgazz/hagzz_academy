<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerShiftClosing extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
        'total_cash_system' => 'decimal:2',
        'total_card_system' => 'decimal:2',
        'total_instapay_system' => 'decimal:2',
        'total_fawry_system' => 'decimal:2',
        'total_bank_system' => 'decimal:2',
        'total_other_system' => 'decimal:2',
        'total_discounts_system' => 'decimal:2',
        'total_collected_system' => 'decimal:2',
        'actual_cash_counted' => 'decimal:2',
        'cash_difference' => 'decimal:2',
    ];

    public function academy()
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function partnerUser()
    {
        return $this->belongsTo(PartnerUser::class, 'partner_user_id');
    }
}
