<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerShiftClosing extends Model
{
    protected $fillable = [
        'academy_id',
        'partner_user_id',
        'closed_by_name',
        'shift_title',
        'started_at',
        'closed_at',
        'total_cash_system',
        'total_card_system',
        'total_instapay_system',
        'total_fawry_system',
        'total_bank_system',
        'total_other_system',
        'total_discounts_system',
        'total_collected_system',
        'actual_cash_counted',
        'cash_difference',
        'next_shift_receiver',
        'notes',
        'status',
    ];

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
