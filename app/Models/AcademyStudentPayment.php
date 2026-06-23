<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyStudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_student_subscription_id',
        'amount',
        'paid_at',
        'method',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AcademyStudentSubscription::class, 'academy_student_subscription_id');
    }
}
