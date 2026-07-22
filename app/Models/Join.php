<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Join extends Model
{

    protected $fillable = [
        'user_id',
        'academy_student_id',
        'training_id',
        'invoice_id',
        'price',
        'net_amount'
    ];

    protected $hidden = ['created_at', 'updated_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);

    }
}
