@php
    $authUser = auth('academy')->user();
    $academy = $authUser?->academy ?: $authUser;
    $subscription = $academy?->currentSubscription;
    $daysRemaining = null;
    $isExpiringSoon = false;
    $isExpiredOrSuspended = false;

    if ($subscription) {
        if (in_array($subscription->status, ['suspended', 'cancelled'])) {
            $isExpiredOrSuspended = true;
        } elseif ($subscription->ends_at) {
            $endsAt = \Carbon\Carbon::parse($subscription->ends_at);
            $now = \Carbon\Carbon::now();
            if ($now->gt($endsAt)) {
                $isExpiredOrSuspended = true;
            } else {
                $daysRemaining = (int) $now->diffInDays($endsAt, false);
                if ($daysRemaining <= 7) {
                    $isExpiringSoon = true;
                }
            }
        }
    } elseif ($academy && $academy->status === 'inactive') {
        $isExpiredOrSuspended = true;
    }
@endphp

@if($isExpiringSoon)
    <div class="alert alert-warning border-0 shadow-sm rounded-3 p-3 my-3 d-flex flex-wrap align-items-center justify-content-between" style="background: linear-gradient(135deg, #fff3cd 0%, #ffecb5 100%); color: #664d03;">
        <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
            <div class="rounded-circle bg-warning bg-opacity-25 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px;">
                <i class="fa-solid fa-clock fa-lg text-warning-emphasis"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> تنبيه: قرب انتهاء باقة اشتراك Hagzz</h6>
                <p class="mb-0 small opacity-90">
                    ينتهي اشتراك المنشأة خلال <strong>{{ $daysRemaining }} أيام</strong> (بتاريخ {{ \Carbon\Carbon::parse($subscription->ends_at)->format('Y-m-d') }}). يُرجى التجديد مبكراً لضمان عدم توقف خدمات الفروع والحجوزات.
                </p>
            </div>
        </div>
        <div>
            <a href="https://wa.me/966500000000?text=أرغب%20في%20تجديد%20اشتراك%20الأكاديمية%20{{ urlencode($academy->commercial_name) }}" target="_blank" class="btn btn-warning btn-sm fw-bold px-3 py-2 text-dark shadow-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> تجديد الاشتراك الآن
            </a>
        </div>
    </div>
@elseif($isExpiredOrSuspended)
    <div class="alert alert-danger border-0 shadow-sm rounded-3 p-3 my-3 d-flex flex-wrap align-items-center justify-content-between" style="background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); color: #842029;">
        <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
            <div class="rounded-circle bg-danger bg-opacity-25 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px;">
                <i class="fa-solid fa-circle-xmark fa-lg text-danger"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1"><i class="fa-solid fa-lock me-1"></i> الاشتراك موقوف أو منتهي الصلاحية</h6>
                <p class="mb-0 small opacity-90">
                    حساب المنشأة موقوف حالياً بسبب انتهاء فترة الاشتراك أو عدم التجديد. يرجى التواصل مع إدارة المبيعات والدعم لتفعيل الحساب واستعادة كامل الصلاحيات.
                </p>
            </div>
        </div>
        <div>
            <a href="https://wa.me/966500000000?text=طلب%20إعادة%20تفعيل%20اشتراك%20الأكاديمية%20{{ urlencode($academy->commercial_name) }}" target="_blank" class="btn btn-danger btn-sm fw-bold px-3 py-2 shadow-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> التواصل مع الدعم للتفعيل
            </a>
        </div>
    </div>
@endif
