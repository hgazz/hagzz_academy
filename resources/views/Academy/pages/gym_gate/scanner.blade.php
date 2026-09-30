@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'ماسح بوابة الصالة الرياضية' : 'Gym Gate Scanner')

@push('css')
    <link href="{{ asset('assetsAdmin/src/assets/css/heroui-theme.css') }}" rel="stylesheet">
    <style>
        .gate-page {
            max-width: 1100px;
            margin: 0 auto;
        }
        .gate-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .gate-status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 1.6s infinite;
        }
        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .gate-grid {
            display: grid;
            grid-template-columns: 430px 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }
        @media(max-width: 880px) {
            .gate-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Input Card */
        .gate-card {
            background: #ffffff;
            border: 1px solid var(--heroui-border, #e2e8f0);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .gate-card h2 {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 0 0 16px;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Gate Stat Pills */
        .gate-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }
        .gate-stat-pill.success {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0 !important;
        }
        .gate-stat-pill.success .pill-count {
            background-color: #059669;
            color: #ffffff !important;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
        }
        .gate-stat-pill.primary {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            border: 1px solid #bfdbfe !important;
        }
        .gate-stat-pill.primary .pill-count {
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .gate-code-wrap {
            position: relative;
        }
        .gate-code-wrap input {
            width: 100%;
            padding: 14px 46px 14px 16px !important;
            border: 2px solid #0f766e;
            border-radius: 10px;
            font: 800 16px monospace;
            direction: ltr !important;
            text-align: left !important;
            transition: all 0.2s ease;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1);
        }
        .gate-code-wrap input:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 5px rgba(15, 118, 110, 0.2);
        }
        .gate-code-wrap .scan-icon {
            position: absolute;
            right: 14px !important;
            left: auto !important;
            top: 50%;
            transform: translateY(-50%);
            color: #0f766e;
            font-size: 18px;
            pointer-events: none;
        }

        .gate-submit {
            width: 100%;
            min-height: 50px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.25);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .gate-submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 118, 110, 0.35);
        }
        .gate-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .camera-toggle-btn {
            width: 100%;
            border: 1px dashed #0f766e;
            border-radius: 8px;
            background: #f0fdf4;
            color: #0f766e;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .camera-toggle-btn:hover {
            background: #dcfce7;
        }

        #cameraPreviewBox {
            display: none;
            margin-top: 12px;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            position: relative;
        }
        #cameraPreviewBox video {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        /* Result Panel */
        .gate-result {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 420px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .gate-result.is-entry {
            border-color: #86efac;
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
        }
        .gate-result.is-exit {
            border-color: #93c5fd;
            background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
        }
        .gate-result.is-warn {
            border-color: #fde68a;
            background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
        }
        .gate-result.is-frozen {
            border-color: #7dd3fc;
            background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%);
        }
        .gate-result.is-suspended, .gate-result.is-error {
            border-color: #fca5a5;
            background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%);
        }

        .gate-result .avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15);
            margin-bottom: 12px;
        }
        .gate-result .member-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
        }
        .gate-result .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 22px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 15px;
            margin: 12px 0;
        }
        .action-entry { background: #dcfce7; color: #166534; }
        .action-exit { background: #dbeafe; color: #1e40af; }
        .action-warn { background: #fef9c3; color: #854d0e; }
        .action-error { background: #fee2e2; color: #991b1b; }
        .action-frozen { background: #e0f2fe; color: #0284c7; }
        .action-suspended { background: #fee2e2; color: #991b1b; }

        .gate-result .duration {
            font-size: 28px;
            font-weight: 900;
            color: #1e40af;
            margin: 6px 0;
        }
        .sessions-remain {
            font-size: 13px;
            background: #ede9fe;
            color: #5b21b6;
            padding: 4px 14px;
            border-radius: 12px;
            display: inline-block;
            margin-top: 6px;
            font-weight: 600;
        }

        /* Log Panel */
        .gate-log {
            background: #ffffff;
            border: 1px solid var(--heroui-border, #e2e8f0);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .gate-log h3 {
            font-size: 15px;
            font-weight: 800;
            margin: 0 0 14px;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .log-list {
            display: grid;
            gap: 8px;
            max-height: 280px;
            overflow-y: auto;
        }
        .log-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 13px;
            border: 1px solid #f1f5f9;
        }
        .log-item .dir-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .dir-in { background: #dcfce7; color: #166534; }
        .dir-out { background: #dbeafe; color: #1e40af; }
        .dir-warn { background: #fef9c3; color: #854d0e; }
        .dir-frozen { background: #e0f2fe; color: #0284c7; }
        .dir-suspended { background: #fee2e2; color: #991b1b; }
        .log-item .log-name { font-weight: 700; color: #0f172a; flex: 1; }
        .log-item .log-time { color: #64748b; font-size: 12px; font-family: monospace; white-space: nowrap; }
    </style>
@endpush

@php
    $ar = app()->getLocale() === 'ar';
@endphp

@section('content')
<div class="middle-content container-xxl p-0 heroui-wrapper gate-page" dir="{{ $ar ? 'rtl' : 'ltr' }}">

    <!-- Breadcrumbs -->
    <div class="secondary-nav mb-3">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </a>
                <div class="d-flex breadcrumb-content">
                    <div class="page-header">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $ar ? 'ماسح بوابة الدخول السريع' : 'Gate Scanner' }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </header>
        </div>
    </div>

    <!-- HeroUI Header Banner -->
    <div class="heroui-header-banner py-3 px-4 mb-3" style="border-radius: 12px;">
        <div class="d-flex align-items-center gap-3">
            <div class="title-icon" style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <h1 class="heroui-header-title m-0" style="font-size: 1.25rem;">{{ $ar ? 'ماسح بوابة الصالة الرياضية' : 'Gym Gate Scanner' }}</h1>
                <p class="heroui-header-subtitle m-0 mt-1" style="font-size: 0.82rem; color: #64748b;">
                    {{ $ar ? 'امسح كارت العضو بقارئ الباركود أو الـ QR — يتحقق النظام تلقائياً من سريان العضوية ويسجل وقت الدخول' : 'Scan member card with barcode/QR reader — system verifies membership and logs entry time' }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
            <div class="d-flex gap-2">
                <div class="gate-stat-pill success">
                    <i class="fa-solid fa-arrow-right-to-bracket text-success"></i>
                    <span>{{ $ar ? 'دخول اليوم:' : "Today's:" }}</span>
                    <span id="stat-entries" class="pill-count">{{ $todayStats['entries'] }}</span>
                </div>
                <div class="gate-stat-pill primary">
                    <i class="fa-solid fa-users text-primary"></i>
                    <span>{{ $ar ? 'داخل الصالة:' : 'Inside:' }}</span>
                    <span id="stat-inside" class="pill-count">{{ $todayStats['inside'] }}</span>
                </div>
            </div>
            <a href="{{ route('academy.gym-gate.log') }}" class="btn btn-sm btn-outline-secondary px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-clipboard-list me-1"></i> {{ $ar ? 'سجل الحركات الكامل' : 'Full Log' }}
            </a>
        </div>
    </div>

    <div class="gate-grid">
        <!-- Input Card -->
        <div class="gate-card">
            <h2>
                <span><i class="fa-solid fa-barcode text-primary me-2 ms-1"></i> {{ $ar ? 'مسح الكارت / الباركود' : 'Scan Card' }}</span>
                <span class="gate-status-pill">
                    <span class="gate-status-dot"></span>
                    <span>{{ $ar ? 'جاهز للاستقبال' : 'Ready' }}</span>
                </span>
            </h2>

            <div>
                <label for="gateCode" class="fw-bold mb-1" style="font-size: 13px;">
                    {{ $ar ? 'وجه الماسح للكارت أو أدخل الكود / الهاتف:' : 'Scan card or enter code/phone:' }}
                </label>
                <div class="gate-code-wrap">
                    <input id="gateCode" class="scan-code" autocomplete="off" placeholder="HZ-..." autofocus>
                    <span class="scan-icon"><i class="fa-solid fa-qrcode"></i></span>
                </div>
            </div>

            <div class="mt-3">
                <label for="gateStation" class="text-muted mb-1" style="font-size: 12px;">{{ $ar ? 'نقطة المسح (اختياري)' : 'Station (optional)' }}</label>
                <input id="gateStation" type="text" class="form-control form-control-sm" style="border-radius: 8px;"
                       placeholder="{{ $ar ? 'الباب الرئيسي / الاستقبال' : 'Main Gate / Reception' }}">
            </div>

            <button class="gate-submit" id="gateBtn" onclick="doScan()">
                <i class="fa-solid fa-bolt"></i>
                <span>{{ $ar ? 'تسجيل الدخول / الخروج' : 'Record Entry / Exit' }}</span>
            </button>

            <!-- Camera Scan Option -->
            <button type="button" class="camera-toggle-btn" id="toggleCameraBtn" onclick="toggleCameraScanner()">
                <i class="fa-solid fa-camera"></i>
                <span id="cameraBtnText">{{ $ar ? 'تشغيل كاميرا الجهاز للمسح (Webcam)' : 'Open Device Camera' }}</span>
            </button>

            <div id="cameraPreviewBox">
                <video id="cameraVideo" playsinline></video>
                <div style="position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%); color: #fff; font-size: 11px; background: rgba(0,0,0,0.6); padding: 2px 10px; border-radius: 12px;">
                    {{ $ar ? 'وجه QR كارت العضو نحو الكاميرا' : 'Align QR code in front of camera' }}
                </div>
            </div>
        </div>

        <!-- Result Panel -->
        <div class="gate-result" id="gateResult">
            <div style="color: #94a3b8;">
                <div style="font-size: 54px; margin-bottom: 10px; color: #cbd5e1;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h3 style="color: #64748b; font-size: 18px; font-weight: 700;">{{ $ar ? 'في انتظار تمرير الكارت' : 'Waiting for card scan' }}</h3>
                <p style="font-size: 13px; color: #94a3b8; max-width: 280px; margin: 0 auto;">
                    {{ $ar ? 'مرر كارت العضو أمام جهاز الباركود أو الكاميرا، وستظهر بياناته وحالة اشتراكه هنا فوراً.' : 'Present member card to scanner or camera. Verification will display here instantly.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Scans Panel -->
    <div class="gate-log mb-4">
        <h3>
            <span><i class="fa-solid fa-clock-rotate-left text-primary me-2 ms-1"></i> {{ $ar ? 'آخر حركات الدخول والخروج' : "Recent Scans" }}</span>
            <small class="text-muted fw-normal" style="font-size: 12px;">{{ $ar ? 'تحديث لحظي' : 'Realtime update' }}</small>
        </h3>
        <div class="log-list" id="gateLog">
            <p style="color: #94a3b8; font-size: 13px; text-align: center; margin: 20px 0;">{{ $ar ? 'لا توجد عمليات دخول حتى الآن.' : 'No scans yet today.' }}</p>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('gateCode');

    // Auto-refocus logic so clerk never needs to click the input field
    function refocus() {
        setTimeout(() => input?.focus(), 150);
    }
    refocus();

    // Trigger scan on Enter from USB scanner or keyboard
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            doScan();
        }
    });

    // Re-focus when clicking anywhere in window if not selecting text
    document.addEventListener('click', (e) => {
        if (!['INPUT', 'BUTTON', 'A', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) {
            refocus();
        }
    });
});

async function doScan(overrideCode = null) {
    const codeInput = document.getElementById('gateCode');
    const code = (overrideCode || codeInput.value).trim();
    const station = document.getElementById('gateStation')?.value.trim() || '';
    const btn = document.getElementById('gateBtn');
    const ar = @json($ar);

    if (!code) return;

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>${ar ? 'جاري التحقق...' : 'Verifying...'}</span>`;

    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));

    // Audio Feedback using Web Audio API (No external sound files required)
    let audioCtx = null;
    function playGateSound(type) {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            if (type === 'entry_success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, now);
                osc.frequency.setValueAtTime(880, now + 0.1);
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } else if (type === 'exit') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(659.25, now);
                osc.frequency.setValueAtTime(523.25, now + 0.1);
                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc.start(now);
                osc.stop(now + 0.3);
            } else if (type === 'frozen') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(784, now);
                osc.frequency.setValueAtTime(587, now + 0.12);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc.start(now);
                osc.stop(now + 0.4);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(320, now);
                osc.frequency.setValueAtTime(220, now + 0.14);
                gain.gain.setValueAtTime(0.2, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc.start(now);
                osc.stop(now + 0.4);
            }
        } catch(e) {}
    }

    try {
        const resp = await fetch('{{ route("academy.gym-gate.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({ code, station, method: overrideCode ? 'camera' : 'qr' }),
        });

        const data = await resp.json();
        if (!resp.ok) throw new Error(data.message || (ar ? 'خطأ في المسح' : 'Scan error'));

        const isEntry = data.direction === 'in';
        const isFrozen = data.status === 'frozen' || data.subscription?.is_frozen;
        const isSuspended = data.status === 'suspended' || data.subscription?.is_suspended;
        const isValid = data.subscription?.valid;
        const resultEl = document.getElementById('gateResult');

        let resClass = 'is-warn';
        let actionClass = 'action-warn';
        let actionIcon = '<i class="fa-solid fa-triangle-exclamation"></i>';

        if (!isEntry) {
            resClass = 'is-exit';
            actionClass = 'action-exit';
            actionIcon = '<i class="fa-solid fa-door-open"></i>';
            playGateSound('exit');
        } else if (isFrozen) {
            resClass = 'is-frozen';
            actionClass = 'action-frozen';
            actionIcon = '<i class="fa-solid fa-snowflake"></i>';
            playGateSound('frozen');
        } else if (isSuspended) {
            resClass = 'is-suspended';
            actionClass = 'action-suspended';
            actionIcon = '<i class="fa-solid fa-ban"></i>';
            playGateSound('warn');
        } else if (isValid) {
            resClass = 'is-entry';
            actionClass = 'action-entry';
            actionIcon = '<i class="fa-solid fa-circle-check"></i>';
            playGateSound('entry_success');
        } else {
            resClass = 'is-warn';
            actionClass = 'action-warn';
            actionIcon = '<i class="fa-solid fa-triangle-exclamation"></i>';
            playGateSound('warn');
        }

        resultEl.className = 'gate-result ' + resClass;
        const actionText = data.message;

        const sessHtml = (data.subscription?.sessions_remaining !== null && data.subscription?.sessions_remaining !== undefined)
            ? `<div class="sessions-remain">${ar ? 'حصص متبقية: ' : 'Sessions left: '}<b>${data.subscription.sessions_remaining}</b></div>`
            : '';

        const durationHtml = !isEntry && data.duration
            ? `<div class="duration">${data.duration} <small style="font-size: 16px;">${ar ? 'دقيقة' : 'min'}</small></div>`
            : '';

        let subStatusDesc = '';
        if (isFrozen) {
            subStatusDesc = (ar ? '❄️ العضوية مجمدة حتى ' : '❄️ Frozen until ') + esc(data.subscription?.frozen_until || '-');
        } else if (isSuspended) {
            subStatusDesc = ar ? '⛔ حساب العضو موقوف إدارياً' : '⛔ Member account is suspended';
        } else if (isValid) {
            subStatusDesc = (ar ? '✅ الاشتراك سارٍ حتى ' : '✅ Valid until ') + esc(data.subscription?.ends_on || '-');
        } else {
            subStatusDesc = ar ? '⚠️ الاشتراك منتهٍ أو غير نشط' : '⚠️ Subscription expired or inactive';
        }

        let guestPassHtml = '';
        if (data.subscription?.guest_visits_remaining !== undefined && data.subscription?.guest_visits_remaining > 0 && isValid) {
            guestPassHtml = `
                <div class="guest-pass-card mt-3 p-2 px-3 rounded-3" style="background: #fdf4ff; border: 1px solid #f0abfc; max-width: 380px; width: 100%;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span style="color: #86198f; font-weight: 700; font-size: 13px;">
                            <i class="fa-solid fa-users me-1 ms-1"></i> ${ar ? 'زيارات المرافقين المتاحة' : 'Guest Passes'}:
                        </span>
                        <span id="guest-remaining-badge" style="background: #c026d3; color: #fff; font-weight: 800; font-size: 12px; padding: 2px 8px; border-radius: 12px;">
                            ${data.subscription.guest_visits_remaining} ${ar ? 'زيارة' : 'left'}
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm w-100 fw-bold" id="btnRegisterGuest" onclick="recordGuestEntry(${data.student.id})" style="background: linear-gradient(135deg, #a21caf, #c026d3); color: #fff; border-radius: 8px; font-size: 13px; box-shadow: 0 2px 6px rgba(162, 28, 175, 0.25);">
                        <i class="fa-solid fa-user-plus me-1 ms-1"></i> ${ar ? 'تسجيل دخول مرافق مع العضو' : 'Check-in Guest'}
                    </button>
                </div>
            `;
        }

        resultEl.innerHTML = `
            <img class="avatar" src="${esc(data.student.image)}" onerror="this.src='${esc(data.student.fallback)}'" alt="">
            <div class="member-name">${esc(data.student.name)}</div>
            <div class="text-muted small mb-1" style="direction: ltr;"><i class="fa-solid fa-phone me-1"></i>${esc(data.student.phone || '—')}</div>
            <div class="action-badge ${actionClass}">${actionIcon} ${esc(actionText)}</div>
            ${durationHtml}
            <div class="fw-semibold text-secondary small">${subStatusDesc}</div>
            ${sessHtml}
            ${guestPassHtml}
        `;

        updateStats(data);
        addToLog(data, esc);

    } catch (err) {
        playGateSound('warn');
        const resultEl = document.getElementById('gateResult');
        resultEl.className = 'gate-result is-error';
        resultEl.innerHTML = `
            <div style="font-size: 50px; color: #dc2626; margin-bottom: 10px;"><i class="fa-solid fa-circle-xmark"></i></div>
            <h3 style="color: #991b1b; font-weight: 800;">${ar ? 'تعذر التحقق' : 'Scan Failed'}</h3>
            <p class="text-muted mb-0" style="max-width: 300px;">${esc(err.message)}</p>
        `;
    } finally {
        codeInput.value = '';
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-bolt"></i> <span>${ar ? 'تسجيل الدخول / الخروج' : 'Record Entry / Exit'}</span>`;
        setTimeout(() => codeInput.focus(), 150);
    }
}

async function recordGuestEntry(studentId) {
    const btn = document.getElementById('btnRegisterGuest');
    const station = document.getElementById('gateStation')?.value || '';
    const ar = @json($ar);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> ${ar ? 'جاري تسجيل المرافق...' : 'Recording...'}`;
    }

    try {
        const resp = await fetch('{{ route("academy.gym-gate.guest-entry") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({ student_id: studentId, station: station }),
        });

        const resData = await resp.json();
        if (!resp.ok) throw new Error(resData.message || (ar ? 'تعذر تسجيل المرافق' : 'Failed to record guest'));

        // Voice / sound effect
        playGateSound('entry_success');

        // Update badge counter
        const badge = document.getElementById('guest-remaining-badge');
        if (badge) {
            badge.textContent = `${resData.guest_visits_remaining} ${ar ? 'زيارة' : 'left'}`;
        }

        // Add to live log
        const log = document.getElementById('gateLog');
        const emptyMsg = log.querySelector('p');
        if (emptyMsg) emptyMsg.remove();
        const time = new Date().toLocaleTimeString();

        log.insertAdjacentHTML('afterbegin', `
            <div class="log-item" style="background: #fdf4ff; border-color: #f0abfc;">
                <div class="dir-icon" style="background: #fae8ff; color: #a21caf;"><i class="fa-solid fa-user-plus"></i></div>
                <span class="log-name fw-bold" style="color: #701a75;">${esc(resData.guest_note)}</span>
                <span style="background-color: #f5d0fe; color: #86198f; border: 1px solid #e879f9; border-radius: 6px; font-weight: 700; padding: 2px 8px; font-size: 11px;">
                    ${ar ? 'دخول مرافق 👥' : 'Guest Entry'}
                </span>
                <span class="log-time">${time}</span>
            </div>
        `);

        // Update entries stats counter
        const entriesCounter = document.getElementById('stat-entries');
        if (entriesCounter) {
            entriesCounter.textContent = parseInt(entriesCounter.textContent || 0) + 1;
        }

        if (btn) {
            btn.className = 'btn btn-sm w-100 fw-bold btn-success';
            btn.style.background = '#15803d';
            btn.innerHTML = `<i class="fa-solid fa-check me-1"></i> ${ar ? 'تم تسجيل دخول المرافق بنجاح' : 'Guest Checked-in!'}`;
            setTimeout(() => {
                if (resData.guest_visits_remaining > 0) {
                    btn.disabled = false;
                    btn.className = 'btn btn-sm w-100 fw-bold';
                    btn.style.background = 'linear-gradient(135deg, #a21caf, #c026d3)';
                    btn.innerHTML = `<i class="fa-solid fa-user-plus me-1 ms-1"></i> ${ar ? 'تسجيل مرافق آخر' : 'Check-in Another Guest'}`;
                } else {
                    btn.disabled = true;
                    btn.innerHTML = ar ? 'نفد رصيد المرافقين' : 'No Guest Passes Left';
                }
            }, 2200);
        }

    } catch (err) {
        alert(err.message);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-user-plus me-1 ms-1"></i> ${ar ? 'تسجيل دخول مرافق مع العضو' : 'Check-in Guest'}`;
        }
    }
}

function addToLog(data, esc) {
    const log = document.getElementById('gateLog');
    const ar = @json($ar);
    const isEntry = data.direction === 'in';
    const isFrozen = data.status === 'frozen' || data.subscription?.is_frozen;
    const isSuspended = data.status === 'suspended' || data.subscription?.is_suspended;
    const isValid = data.subscription?.valid;

    let icon = isEntry ? (isValid ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>') : '<i class="fa-solid fa-arrow-right-from-bracket"></i>';
    let dirClass = isEntry ? (isValid ? 'dir-in' : 'dir-warn') : 'dir-out';
    if (isFrozen) {
        icon = '<i class="fa-solid fa-snowflake"></i>';
        dirClass = 'dir-frozen';
    } else if (isSuspended) {
        icon = '<i class="fa-solid fa-ban"></i>';
        dirClass = 'dir-suspended';
    }
    const time = new Date().toLocaleTimeString();

    const emptyMsg = log.querySelector('p');
    if (emptyMsg) emptyMsg.remove();

    log.insertAdjacentHTML('afterbegin', `
        <div class="log-item">
            <div class="dir-icon ${dirClass}">${icon}</div>
            <span class="log-name">${esc(data.student.name)}</span>
            <span style="background-color: ${isEntry ? '#dcfce7' : '#dbeafe'}; color: ${isEntry ? '#15803d' : '#1d4ed8'}; border: 1px solid ${isEntry ? '#86efac' : '#93c5fd'}; border-radius: 6px; font-weight: 700; padding: 2px 8px; font-size: 11px;">
                ${isEntry ? (ar ? 'دخول' : 'Entry') : (ar ? 'خروج' : 'Exit')}
            </span>
            <span class="log-time">${time}</span>
        </div>
    `);

    const items = log.querySelectorAll('.log-item');
    if (items.length > 25) items[items.length - 1].remove();
}

function updateStats(data) {
    const inside = document.getElementById('stat-inside');
    const entries = document.getElementById('stat-entries');
    if (data.direction === 'in' && data.subscription?.valid) {
        if (inside) inside.textContent = parseInt(inside.textContent || 0) + 1;
        if (entries) entries.textContent = parseInt(entries.textContent || 0) + 1;
    } else if (data.direction === 'out') {
        if (inside) inside.textContent = Math.max(0, parseInt(inside.textContent || 0) - 1);
    }
}

/* Optional Camera Scanner Support */
let cameraStream = null;
let cameraScanning = false;

async function toggleCameraScanner() {
    const previewBox = document.getElementById('cameraPreviewBox');
    const video = document.getElementById('cameraVideo');
    const btnText = document.getElementById('cameraBtnText');
    const ar = @json($ar);

    if (cameraStream) {
        // Stop camera
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
        cameraScanning = false;
        previewBox.style.display = 'none';
        btnText.textContent = ar ? 'تشغيل كاميرا الجهاز للمسح (Webcam)' : 'Open Device Camera';
        return;
    }

    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });
        video.srcObject = cameraStream;
        await video.play();
        previewBox.style.display = 'block';
        btnText.textContent = ar ? 'إيقاف الكاميرا' : 'Stop Camera';
        cameraScanning = true;

        if ('BarcodeDetector' in window) {
            const detector = new BarcodeDetector({ formats: ['qr_code', 'code_128', 'ean_13', 'ean_8'] });
            const scanFrame = async () => {
                if (!cameraScanning) return;
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes.length > 0) {
                        const raw = barcodes[0].rawValue;
                        doScan(raw);
                        // small pause after successful scan
                        await new Promise(r => setTimeout(r, 2000));
                    }
                } catch(e) {}
                if (cameraScanning) requestAnimationFrame(scanFrame);
            };
            requestAnimationFrame(scanFrame);
        } else {
            console.log('Native BarcodeDetector not available in this browser');
        }
    } catch(e) {
        alert(ar ? 'تعذر فتح الكاميرا: يرجى التحقق من أذونات المتصفح' : 'Could not open camera: Check browser permissions');
    }
}
</script>
@endpush
