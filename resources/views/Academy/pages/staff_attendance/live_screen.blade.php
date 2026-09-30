@php
    $ar = app()->getLocale() === 'ar';
    $facType = \App\Support\FacilityTerminology::currentType();
    $termTrainer = facility_term('coach', $facType, false);
    $termTrainers = facility_term('coach', $facType, true);
    $academyName = $academy->commercial_name ?: 'Hagzz Club';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ $ar ? 'شاشة الحضور الذكية | ' . $academyName : 'Live Attendance Screen | ' . $academyName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Light Mode Variables (Default) */
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --bg-card-subtle: #f8fafc;
            --bg-nav: #ffffff;
            --border-color: #e2e8f0;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-dim: #94a3b8;
            --accent-green: #059669;
            --accent-green-bg: #ecfdf5;
            --accent-green-border: #a7f3d0;
            --accent-glow: rgba(5, 150, 105, 0.15);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.04);
            --feed-item-bg: #f8fafc;
            --feed-item-hover: #f1f5f9;
            --clock-color: #0f172a;
            --qr-box-border: #e2e8f0;
            --footer-bg: #ffffff;
        }

        /* Dark Mode Variables */
        body.dark, html.dark {
            --bg-body: #0b1120;
            --bg-card: #151f32;
            --bg-card-subtle: #1c283f;
            --bg-nav: rgba(15, 23, 42, 0.95);
            --border-color: rgba(255, 255, 255, 0.12);
            --text-heading: #ffffff;
            --text-body: #e2e8f0;
            --text-muted: #cbd5e1;
            --text-dim: #94a3b8;
            --accent-green: #10b981;
            --accent-green-bg: rgba(16, 185, 129, 0.18);
            --accent-green-border: rgba(16, 185, 129, 0.4);
            --accent-glow: rgba(16, 185, 129, 0.25);
            --card-shadow: 0 15px 40px rgba(0,0,0,0.4);
            --feed-item-bg: #1e293b;
            --feed-item-hover: #27354f;
            --clock-color: #ffffff;
            --qr-box-border: transparent;
            --footer-bg: rgba(15, 23, 42, 0.95);
        }

        * { box-sizing: border-box; }
        
        body {
            background-color: var(--bg-body);
            color: var(--text-body);
            font-family: 'Cairo', 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Top Navigation Bar */
        .live-navbar {
            background: var(--bg-nav);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 20px;
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .live-badge {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.5px;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.5; }
            100% { transform: scale(0.9); opacity: 1; }
        }

        /* Main Container & Grid */
        .live-main {
            flex: 1;
            padding: 20px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Primary QR Card */
        .qr-card-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 24px 20px;
            box-shadow: var(--card-shadow);
            text-align: center;
            max-width: 440px;
            margin: 0 auto;
            position: relative;
            transition: all 0.3s ease;
        }

        body.dark .qr-card-container {
            border: 1.5px solid rgba(16, 185, 129, 0.35);
            box-shadow: var(--card-shadow), 0 0 35px var(--accent-glow);
        }

        .qr-badge-pill {
            background: var(--accent-green-bg);
            color: var(--accent-green);
            border: 1px solid var(--accent-green-border);
            padding: 5px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .qr-canvas-box {
            background: #ffffff;
            border-radius: 18px;
            padding: 14px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 16px auto;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border: 1px solid var(--qr-box-border);
            transition: all 0.3s ease;
        }

        /* Countdown Progress Bar */
        .countdown-bar-container {
            background: var(--border-color);
            height: 7px;
            border-radius: 999px;
            overflow: hidden;
            margin: 14px 0 8px;
        }

        .countdown-bar-fill {
            background: linear-gradient(90deg, #10b981, #34d399);
            height: 100%;
            width: 100%;
            transition: width 1s linear;
        }

        /* Clock & Stats Widgets */
        .side-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }

        .clock-display {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 1px;
            color: var(--clock-color);
            font-variant-numeric: tabular-nums;
            line-height: 1.1;
        }

        /* Feed List */
        .feed-card {
            background: var(--feed-item-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 10px 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }

        .feed-card:hover {
            background: var(--feed-item-hover);
        }

        /* Theme Toggle Button */
        .btn-theme-toggle {
            background: var(--bg-card-subtle);
            border: 1px solid var(--border-color);
            color: var(--text-heading);
            border-radius: 12px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-theme-toggle:hover {
            background: var(--border-color);
            color: var(--accent-green);
        }

        /* High-Contrast Helpers */
        .text-c-heading { color: var(--text-heading) !important; }
        .text-c-body    { color: var(--text-body) !important; }
        .text-c-muted   { color: var(--text-muted) !important; }
        .text-c-dim     { color: var(--text-dim) !important; }

        .info-note-box {
            background: var(--bg-card-subtle);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 12px;
            color: var(--text-muted);
            font-size: 11.5px;
            line-height: 1.5;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .live-navbar {
                padding: 10px 12px;
            }
            .live-main {
                padding: 12px 8px;
            }
            .qr-card-container {
                padding: 20px 16px;
                border-radius: 20px;
                max-width: 100%;
            }
            .qr-canvas-box {
                padding: 10px;
                margin: 12px auto;
            }
            .side-card {
                padding: 16px;
                border-radius: 18px;
            }
            .clock-display {
                font-size: 26px;
            }
            .app-title-header {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navbar with Theme Switcher -->
    <header class="live-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="live-badge">
                <span class="pulse-dot"></span> LIVE
            </div>
            <h5 class="mb-0 fw-bold text-c-heading app-title-header">{{ $academyName }}</h5>
            <span class="text-c-dim d-none d-md-inline">|</span>
            <span class="fw-bold d-none d-md-inline text-success fs-7">
                <i class="fa-solid fa-location-dot me-1"></i> {{ $selectedBranch?->address ?: ($ar ? 'الفرع الرئيسي' : 'Main Branch') }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($branches->count() > 1)
                <select class="form-select form-select-sm" style="max-width: 140px; background:var(--bg-card); color:var(--text-heading); border-color:var(--border-color); border-radius:10px;" onchange="window.location.href='?branch_id=' + this.value">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBranch?->id == $b->id ? 'selected' : '' }}>{{ $b->address ?: 'فرع #'.$b->id }}</option>
                    @endforeach
                </select>
            @endif

            <!-- Theme Switcher (Dark / Light) -->
            <button type="button" class="btn-theme-toggle" id="themeToggleBtn" title="{{ $ar ? 'تبديل الوضع (نهاري / ليلي)' : 'Toggle Theme' }}">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>

            <!-- Fullscreen Button -->
            <button type="button" class="btn-theme-toggle d-none d-sm-inline-flex" onclick="toggleFullScreen()" title="{{ $ar ? 'ملء الشاشة' : 'Fullscreen' }}">
                <i class="fa-solid fa-expand"></i>
            </button>

            <!-- Back to Dashboard -->
            <a href="{{ route('academy.staff-attendance.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:10px; font-weight:600;">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="d-none d-sm-inline">{{ $ar ? 'الإدارة' : 'Back' }}</span>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="live-main container-xl">
        <div class="row align-items-center justify-content-center g-3">
            
            <!-- Dynamic QR Code Card -->
            <div class="col-lg-5 col-md-6 col-12">
                <div class="qr-card-container">
                    <div class="mb-2">
                        <span class="qr-badge-pill">
                            <i class="fa-solid fa-shield-halved"></i> {{ $ar ? 'كود ذكي متغير (مؤمن ضد التداول)' : 'Anti-Fraud Dynamic QR' }}
                        </span>
                    </div>

                    <h3 class="mt-2 mb-1 fw-bold text-c-heading fs-4">{{ $ar ? 'امسح بجوالك لتسجيل الحضور' : 'Scan to Check-In / Out' }}</h3>
                    <p class="text-c-muted fs-7 mb-2 fw-semibold">
                        {{ $ar ? 'للكادر الإداري، موظفي الاستقبال، و' . $termTrainers : 'For Staff, Receptionists, and ' . $termTrainers }}
                    </p>

                    <!-- QR Box Canvas -->
                    <div class="qr-canvas-box" id="qrContainer">
                        <!-- QR Code JS will inject SVG/Canvas here -->
                    </div>

                    <!-- Countdown Progress Bar -->
                    <div class="countdown-bar-container">
                        <div class="countdown-bar-fill" id="countdownFill"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center text-c-muted fs-7 mb-3 px-1">
                        <span class="fw-semibold"><i class="fa-solid fa-arrows-rotate me-1 text-success"></i> {{ $ar ? 'يتجدد الكود خلال:' : 'Refreshes in:' }}</span>
                        <strong class="text-warning fs-6 fw-bold" id="countdownText">20s</strong>
                    </div>

                    <!-- Security Notice Box -->
                    <div class="info-note-box text-start" dir="{{ $ar ? 'rtl' : 'ltr' }}">
                        <i class="fa-solid fa-circle-info text-info me-1"></i>
                        {{ $ar ? 'الرمز يتغير كل 20 ثانية ولا يمكن تصويره أو مشاركته لضمان التواجد الفعلي داخل المنشأة.' : 'The QR refreshes every 20 seconds to prevent forwarding photos and ensure physical presence.' }}
                    </div>
                </div>
            </div>

            <!-- Side Column: Clock & Recent Activity -->
            <div class="col-lg-4 col-md-6 col-12">
                
                <!-- Digital Clock Card -->
                <div class="side-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-c-muted d-block fs-7 fw-bold mb-1">{{ $ar ? 'التوقيت المحلي بالفرع' : 'Branch Local Time' }}</span>
                            <div class="clock-display" id="liveClock">00:00:00</div>
                        </div>
                        <div class="text-end">
                            <span class="text-success fw-bold d-block fs-6" id="liveDate">{{ date('Y-m-d') }}</span>
                            <small class="text-c-muted fw-semibold">{{ Carbon\Carbon::now()->locale($ar ? 'ar' : 'en')->isoFormat('dddd') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Activity Card -->
                <div class="side-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-c-heading d-flex align-items-center">
                            <i class="fa-solid fa-bolt text-warning me-2"></i> {{ $ar ? 'حركات اليوم بالفرع' : 'Today Check-Ins' }}
                        </h6>
                        <span class="badge" style="background:var(--accent-green-bg); color:var(--accent-green); border:1px solid var(--accent-green-border); font-size:12px;">
                            {{ count($recentCheckins) }} {{ $ar ? 'حركات' : 'logs' }}
                        </span>
                    </div>

                    <div id="recentFeed" style="max-height: 230px; overflow-y: auto;">
                        @forelse($recentCheckins as $entry)
                            <div class="feed-card">
                                <div>
                                    <strong class="d-block text-c-heading fs-7">{{ $entry->staff_name }}</strong>
                                    <small class="text-c-muted fw-semibold">
                                        {{ $entry->staff_type === 'coach' ? $termTrainer : ($ar ? 'موظف' : 'Staff') }} · 
                                        {{ $entry->check_in_at ? $entry->check_in_at->format('H:i') : '' }}
                                    </small>
                                </div>
                                <div>
                                    @if($entry->check_out_at)
                                        <span class="badge" style="background:var(--bg-card-subtle); color:var(--text-muted); border:1px solid var(--border-color); font-size:11px;">
                                            {{ $ar ? 'انصرف ' . $entry->check_out_at->format('H:i') : 'Out ' . $entry->check_out_at->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="badge" style="background:var(--accent-green-bg); color:var(--accent-green); border:1px solid var(--accent-green-border); font-size:11px;">
                                            <i class="fa-solid fa-circle-dot me-1"></i>{{ $ar ? 'متواجد' : 'In Branch' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-c-muted fs-7">
                                <i class="fa-regular fa-clock d-block mb-1 fs-4 text-c-dim"></i>
                                {{ $ar ? 'لم يتم تسجيل حركات حضور اليوم بهذا الفرع بعد.' : 'No check-ins recorded today yet.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-2 text-c-muted fs-7" style="background: var(--footer-bg); border-top: 1px solid var(--border-color);">
        Powered by <strong>Hagzz Smart Attendance</strong> · {{ $academyName }}
    </footer>

    <!-- QR Code Generator Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // 1. Theme (Dark / Light Mode) Management
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');

        function initTheme() {
            // Check project localStorage setting
            let isDark = false;
            try {
                const storedTheme = localStorage.getItem("theme");
                if (storedTheme) {
                    const parsed = JSON.parse(storedTheme);
                    if (parsed?.settings?.layout?.darkMode !== undefined) {
                        isDark = parsed.settings.layout.darkMode;
                    }
                } else if (localStorage.getItem("hagzz_live_theme")) {
                    isDark = localStorage.getItem("hagzz_live_theme") === 'dark';
                } else {
                    // Match system or default to dark for kiosk displays
                    isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
            } catch (e) {
                isDark = false;
            }

            applyTheme(isDark);
        }

        function applyTheme(isDark) {
            if (isDark) {
                document.body.classList.add('dark');
                document.documentElement.classList.add('dark');
                themeIcon.className = 'fa-solid fa-sun text-warning';
            } else {
                document.body.classList.remove('dark');
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'fa-solid fa-moon text-dark';
            }
        }

        themeToggleBtn.addEventListener('click', () => {
            const isCurrentlyDark = document.body.classList.contains('dark');
            const newIsDark = !isCurrentlyDark;
            applyTheme(newIsDark);
            
            // Persist to localStorage
            try {
                localStorage.setItem("hagzz_live_theme", newIsDark ? 'dark' : 'light');
                const storedTheme = localStorage.getItem("theme");
                if (storedTheme) {
                    const parsed = JSON.parse(storedTheme);
                    if (parsed?.settings?.layout) {
                        parsed.settings.layout.darkMode = newIsDark;
                        localStorage.setItem("theme", JSON.stringify(parsed));
                    }
                }
            } catch(e) {}
        });

        initTheme();

        // 2. Dynamic QR Code Lifecycle
        const academyId = {{ (int) $academy->id }};
        const branchId = {{ (int) ($selectedBranch?->id ?? 0) }};
        const refreshUrl = "{{ route('academy.staff-attendance.get-qr-token') }}";

        let currentScanUrl = "{{ $tokenData['scan_url'] }}";
        let secondsRemaining = {{ (int) $tokenData['expires_in'] }};
        const slotTotal = 20;

        const qrContainer = document.getElementById('qrContainer');
        const countdownFill = document.getElementById('countdownFill');
        const countdownText = document.getElementById('countdownText');

        // Dynamic QR code size based on viewport
        const calcQrSize = () => Math.min(220, Math.max(160, window.innerWidth < 480 ? 170 : 210));
        let currentQrSize = calcQrSize();

        let qrcode = new QRCode(qrContainer, {
            text: currentScanUrl,
            width: currentQrSize,
            height: currentQrSize,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });

        // Countdown timer tick
        setInterval(() => {
            secondsRemaining--;
            if (secondsRemaining <= 0) {
                secondsRemaining = slotTotal;
                fetchNewToken();
            }

            const pct = (secondsRemaining / slotTotal) * 100;
            countdownFill.style.width = pct + '%';
            countdownText.textContent = secondsRemaining + 's';
        }, 1000);

        function fetchNewToken() {
            fetch(`${refreshUrl}?branch_id=${branchId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.scan_url) {
                        currentScanUrl = data.scan_url;
                        secondsRemaining = data.expires_in || slotTotal;
                        qrcode.clear();
                        qrcode.makeCode(currentScanUrl);
                    }
                })
                .catch(err => console.error("Error refreshing QR token:", err));
        }

        // Live Clock
        function updateClock() {
            const now = new Date();
            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('liveClock').textContent = `${hrs}:${mins}:${secs}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</body>
</html>
