@php
    $ar = app()->getLocale() === 'ar';
    $academyName = $academy?->commercial_name ?: 'Hagzz';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $success ? ($ar ? 'تم التسجيل بنجاح' : 'Success') : ($ar ? 'تنبيه' : 'Alert') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0b402f 0%, #111827 100%);
            color: #f8fafc;
            font-family: 'Cairo', 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .result-card {
            background: #ffffff;
            color: #0f172a;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            padding: 40px 28px;
            width: 100%;
            max-width: 440px;
            text-align: center;
        }
        .result-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 20px;
        }
        .icon-success { background: #dcfce7; color: #16a34a; }
        .icon-error { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>

    <div class="result-card">
        @if($success)
            <div class="result-icon icon-success">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="fw-bold text-success mb-2">
                {{ ($action_type ?? 'check_in') === 'check_out' ? ($ar ? 'تم تسجيل الانصراف' : 'Check-Out Confirmed') : ($ar ? 'تم تسجيل الحضور بنجاح' : 'Check-In Confirmed') }}
            </h3>
            <h5 class="fw-bold text-dark mb-3">{{ $staff_name ?? '' }}</h5>
            <p class="text-muted fs-6 mb-4">{{ $message }}</p>

            <div class="p-3 bg-light rounded-4 border mb-4 text-start" dir="{{ $ar ? 'rtl' : 'ltr' }}">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-7">{{ $ar ? 'المنشأة:' : 'Facility:' }}</span>
                    <strong class="text-dark fs-7">{{ $academyName }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-7">{{ $ar ? 'الفرع:' : 'Branch:' }}</span>
                    <strong class="text-dark fs-7">{{ $branch?->address ?: ($ar ? 'الرئيسي' : 'Main') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-7">{{ $ar ? 'التوقيت المسجل:' : 'Time:' }}</span>
                    <strong class="text-success fs-7">{{ $time_str ?? now()->format('H:i') }}</strong>
                </div>
                @if(!empty($duration_str))
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="text-muted fs-7">{{ $ar ? 'مدة التواجد:' : 'Duration:' }}</span>
                        <strong class="text-primary fs-7">{{ $duration_str }}</strong>
                    </div>
                @endif
            </div>

            <button type="button" class="btn btn-outline-secondary w-100 fw-bold" onclick="window.close();">
                {{ $ar ? 'إغلاق الشاشة' : 'Close Window' }}
            </button>
        @else
            <div class="result-icon icon-error">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 class="fw-bold text-danger mb-2">{{ $ar ? 'الكود منتهي الصلاحية' : 'QR Expired' }}</h3>
            <p class="text-muted fs-6 mb-4">{{ $message }}</p>

            <a href="javascript:history.back()" class="btn btn-primary w-100 fw-bold py-2" style="border-radius:12px;">
                <i class="fa-solid fa-arrow-rotate-left me-1"></i> {{ $ar ? 'محاولة أخرى' : 'Try Again' }}
            </a>
        @endif
    </div>

</body>
</html>
