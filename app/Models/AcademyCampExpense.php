<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyCampExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_camp_id',
        'category_id',
        'title',
        'amount',
        'currency_code',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function camp(): BelongsTo
    {
        return $this->belongsTo(AcademyCamp::class, 'academy_camp_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartnerExpenseCategory::class, 'category_id');
    }
}
