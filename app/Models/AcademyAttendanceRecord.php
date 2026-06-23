<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademyAttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_attendance_session_id',
        'academy_student_id',
        'status',
        'check_in_at',
        'check_out_at',
        'notes',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademyAttendanceSession::class, 'academy_attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }
}
