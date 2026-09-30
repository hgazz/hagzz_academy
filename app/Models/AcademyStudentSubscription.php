<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyStudentSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_student_id',
        'academy_group_id',
        'starts_on',
        'ends_on',
        'amount',
        'discount_amount',
        'discount_reason',
        'discount_approved_by',
        'discount_approved_at',
        'status',
        'payment_status',
        'notes',
        // حقول التجميد (للجيم والمراكز الصحية)
        'frozen_from',
        'frozen_until',
        'original_ends_on',
        'freeze_reason',
        'frozen_by',
        // رصيد الجلسات (للمراكز الصحية)
        'sessions_total',
        'sessions_used',
        // رصيد زيارات الضيوف والمرافقين (للجيم)
        'guest_visits_total',
        'guest_visits_used',
    ];

    protected $casts = [
        'starts_on'            => 'date',
        'ends_on'              => 'date',
        'original_ends_on'     => 'date',
        'frozen_from'          => 'date',
        'frozen_until'         => 'date',
        'discount_approved_at' => 'datetime',
        'amount'               => 'decimal:2',
        'discount_amount'      => 'decimal:2',
        'sessions_total'       => 'integer',
        'sessions_used'        => 'integer',
        'guest_visits_total'   => 'integer',
        'guest_visits_used'    => 'integer',
    ];

    // ── العلاقات ────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademyStudent::class, 'academy_student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AcademyGroup::class, 'academy_group_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AcademyStudentPayment::class);
    }

    // ── Accessors ──────────────────────────────────────────────────────

    /** المبلغ المدفوع فعلياً */
    public function getPaidAmountAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }
        return (float) $this->payments()->sum('amount');
    }

    /** المبلغ الصافي بعد الخصم */
    public function getNetAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) ($this->discount_amount ?? 0));
    }

    /** المبلغ المتبقي للسداد */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->net_amount - $this->paid_amount);
    }

    /** هل الاشتراك نشط ومسدد (جزئياً أو كاملاً)؟ */
    public function getIsActiveAndValidAttribute(): bool
    {
        return $this->status === 'active'
            && $this->ends_on
            && ($this->ends_on->isToday() || $this->ends_on->isFuture());
    }

    /** هل الاشتراك مجمد حالياً؟ */
    public function getIsFrozenAttribute(): bool
    {
        if ($this->status !== 'frozen') {
            return false;
        }
        if ($this->frozen_from && $this->frozen_from->isFuture()) {
            return false; // تجميد مجدول مستقبلاً
        }
        if ($this->frozen_until && $this->frozen_until->isPast()) {
            return false; // انتهت فترة التجميد
        }
        return true;
    }

    /** عدد الجلسات المتبقية (للمراكز الصحية) */
    public function getRemainingSessionsAttribute(): ?int
    {
        if ($this->sessions_total === null) {
            return null; // اشتراك مفتوح — لا حد للجلسات
        }
        return max(0, (int) $this->sessions_total - (int) $this->sessions_used);
    }

    /** رصيد زيارات الضيوف/المرافقين المتبقية */
    public function getRemainingGuestVisitsAttribute(): int
    {
        if ($this->guest_visits_total !== null) {
            return max(0, (int) $this->guest_visits_total - (int) $this->guest_visits_used);
        }
        return 0;
    }

    /** استهلاك زيارة ضيف/مرافق */
    public function consumeGuestVisit(): bool
    {
        if ($this->guest_visits_total !== null && $this->guest_visits_used < $this->guest_visits_total) {
            $this->increment('guest_visits_used');
            return true;
        }
        return false;
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    /** اشتراكات نشطة وغير منتهية */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('ends_on', '>=', today());
    }

    /** اشتراكات مجمدة حالياً */
    public function scopeFrozen($query)
    {
        return $query->where('status', 'frozen');
    }

    /** اشتراكات منتهية */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'active')->where('ends_on', '<', today());
              });
        });
    }

    // ── تجميد الاشتراك ─────────────────────────────────────────────────

    /**
     * تجميد الاشتراك مع تمديد تاريخ الانتهاء تلقائياً
     *
     * @param string      $reason     سبب التجميد
     * @param int         $days       عدد أيام التجميد
     * @param string|null $frozenBy   اسم من نفّذ التجميد
     */
    public function freeze(string $reason, int $days = 30, ?string $frozenBy = null): static
    {
        $frozenFrom  = today();
        $frozenUntil = today()->addDays($days);

        // احفظ تاريخ الانتهاء الأصلي قبل أول تجميد
        $originalEnds = $this->original_ends_on ?? $this->ends_on;

        // مدد تاريخ الانتهاء بعدد أيام التجميد
        $newEndsOn = $this->ends_on->addDays($days);

        $this->update([
            'status'           => 'frozen',
            'frozen_from'      => $frozenFrom,
            'frozen_until'     => $frozenUntil,
            'original_ends_on' => $originalEnds,
            'ends_on'          => $newEndsOn,
            'freeze_reason'    => $reason,
            'frozen_by'        => $frozenBy ?? auth('academy')->user()?->name ?? 'النظام',
        ]);

        return $this;
    }

    /**
     * إلغاء التجميد وإعادة الاشتراك للحالة النشطة
     */
    public function unfreeze(): static
    {
        if ($this->status !== 'frozen') {
            return $this;
        }

        $this->update([
            'status'       => 'active',
            'frozen_from'  => null,
            'frozen_until' => null,
            'freeze_reason'=> null,
            'frozen_by'    => null,
            // لا نُرجع ends_on — يبقى ممدداً لأن أيام التجميد أُضيفت مسبقاً
        ]);

        return $this;
    }

    /**
     * استهلاك جلسة علاجية (للمراكز الصحية)
     */
    public function consumeSession(): static
    {
        if ($this->sessions_total !== null) {
            $this->increment('sessions_used');
        }
        return $this;
    }
}
