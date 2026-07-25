<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'category_id',
        'title',
        'amount',
        'currency',
        'expense_date',
        'period_type',
        'approved_by',
        'notes',
        'receipt_image',
        'created_by_user_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academies::class, 'academy_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartnerExpenseCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(PartnerUser::class, 'created_by_user_id');
    }
}
