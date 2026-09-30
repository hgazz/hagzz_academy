@php
    $ar = app()->getLocale() === 'ar';
    $facType = \App\Support\FacilityTerminology::currentType();
    $termTrainer = facility_term('coach', $facType, false);
    $termTrainers = facility_term('coach', $facType, true);
    $academyName = $academy->commercial_name ?: 'Hagzz';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ar ? 'تسجيل الحضور بالفرع' : 'Branch Check-In' }}</title>
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
        .scan-card {
            background: #ffffff;
            color: #0f172a;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
            padding: 32px 24px;
            width: 100%;
            max-width: 440px;
        }
    </style>
</head>
<body>

    <div class="scan-card">
        <div class="text-center mb-4">
            <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-size:12px; padding:6px 14px; border-radius:999px;">
                <i class="fa-solid fa-circle-check me-1"></i> {{ $ar ? 'تم التحقق من الرمز بالفرع' : 'Location & QR Verified' }}
            </span>
            <h3 class="fw-bold mt-2 mb-1 text-dark">{{ $academyName }}</h3>
            <p class="text-muted fs-6 mb-0">
                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                {{ $branch?->address ?: ($ar ? 'الفرع الرئيسي' : 'Main Branch') }}
            </p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger py-2 mb-3 fs-7">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('academy.staff-attendance.confirm-scan') }}">
            @csrf
            <input type="hidden" name="academy_id" value="{{ $academy->id }}">
            <input type="hidden" name="branch_id" value="{{ $branch?->id ?? 0 }}">
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="slot" value="{{ $slot }}">

            @if($loggedStaffId)
                <div class="alert alert-success py-2 mb-3 fs-7 text-start">
                    <i class="fa-solid fa-user-shield me-1"></i>
                    {{ $ar ? 'تم التحقق من هويتك كمسجل دخول: ' . auth('academy')->user()->name : 'Authenticated session: ' . auth('academy')->user()->name }}
                </div>
                <input type="hidden" name="staff_target" value="employee:{{ $loggedStaffId }}">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-6">{{ $ar ? 'الموظف المسجل:' : 'Staff Member:' }}</label>
                    <input type="text" class="form-control form-control-lg bg-light" value="{{ auth('academy')->user()->name }}" readonly style="border-radius:12px;">
                </div>
            @else
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-6">{{ $ar ? 'حدد اسمك من القائمة:' : 'Select Your Name:' }} <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg fs-6" name="staff_target" required style="border-radius:12px;">
                        <option value="">{{ $ar ? '-- اختر اسمك --' : '-- Choose Staff Member --' }}</option>
                        <optgroup label="{{ $ar ? 'الكادر الإداري والموظفين' : 'Administrative Staff' }}">
                            @foreach($employees as $emp)
                                <option value="employee:{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="{{ $termTrainers }}">
                            @foreach($coaches as $c)
                                <option value="coach:{{ $c->id }}">{{ $c->name }} ({{ $termTrainer }})</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-6 mb-1">
                        {{ $ar ? 'رمز التحقق الأمني (آخر 4 أرقام من هاتفك):' : 'Security PIN (Last 4 digits of phone):' }} <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control form-control-lg" name="security_pin" placeholder="••••" maxlength="6" required style="border-radius:12px; letter-spacing:3px; text-align:center;">
                    <small class="text-muted d-block mt-1 fs-7">
                        <i class="fa-solid fa-shield-halved me-1 text-success"></i>
                        {{ $ar ? 'مطلوب لمنع تسجيل الحضور بالنيابة عن الزملاء.' : 'Required to prevent buddy check-ins.' }}
                    </small>
                </div>
            @endif

            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3" style="border-radius: 14px; background: #0e5a3f; border-color: #0e5a3f;">
                <i class="fa-solid fa-fingerprint me-2"></i>
                {{ $ar ? 'تسجيل الحضور / الانصراف الآن' : 'Check-In / Out Now' }}
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top text-muted fs-7">
            <i class="fa-solid fa-clock me-1"></i> {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

</body>
</html>
