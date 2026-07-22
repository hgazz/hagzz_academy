@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = $isArabic ? [
        'title' => 'بوابة شركاء Hagzz',
        'eyebrow' => 'مساحة عمل الأكاديمية',
        'hero_title' => 'شغّل أكاديميتك بوضوح أكبر',
        'hero_body' => 'من الطلاب والاشتراكات إلى الحضور والجداول، كل ما يحتاجه فريقك في مساحة واحدة سهلة وآمنة.',
        'feature_students' => 'إدارة الطلاب',
        'feature_attendance' => 'متابعة الحضور',
        'feature_reports' => 'تقارير فورية',
        'card_title' => 'يومك تحت السيطرة',
        'card_body' => 'تابع الحصص والاشتراكات وأداء الأكاديمية لحظة بلحظة.',
        'welcome' => 'مرحبًا بعودتك',
        'welcome_body' => 'سجّل الدخول إلى مساحة عمل الأكاديمية',
        'email' => 'البريد الإلكتروني',
        'email_placeholder' => 'name@academy.com',
        'password' => 'كلمة المرور',
        'password_placeholder' => 'أدخل كلمة المرور',
        'remember' => 'تذكرني على هذا الجهاز',
        'submit' => 'الدخول إلى لوحة الشريك',
        'show' => 'إظهار',
        'hide' => 'إخفاء',
        'security' => 'دخول آمن ومخصص لشركاء Hagzz',
        'help' => 'تحتاج مساعدة؟ تواصل مع مدير حسابك في Hagzz.',
        'language' => 'English',
    ] : [
        'title' => 'Hagzz Partner Portal',
        'eyebrow' => 'Your academy workspace',
        'hero_title' => 'Run your academy with greater clarity',
        'hero_body' => 'From students and subscriptions to attendance and schedules, give your team one simple and secure workspace.',
        'feature_students' => 'Student management',
        'feature_attendance' => 'Attendance tracking',
        'feature_reports' => 'Live reporting',
        'card_title' => 'Stay in control of every day',
        'card_body' => 'Keep classes, subscriptions, and academy performance within reach.',
        'welcome' => 'Welcome back',
        'welcome_body' => 'Sign in to your academy workspace',
        'email' => 'Email address',
        'email_placeholder' => 'name@academy.com',
        'password' => 'Password',
        'password_placeholder' => 'Enter your password',
        'remember' => 'Remember me on this device',
        'submit' => 'Open partner dashboard',
        'show' => 'Show',
        'hide' => 'Hide',
        'security' => 'Secure access for Hagzz partners',
        'help' => 'Need help? Contact your Hagzz account manager.',
        'language' => 'العربية',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#132621">
    <title>{{ $copy['title'] }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assetsAdmin/logo/Tab icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assetsAdmin/src/assets/css/partner-login-modern.css') }}" rel="stylesheet">
</head>
<body class="partner-login-page">
    <main class="partner-login-shell">
        <section class="partner-login-visual" aria-labelledby="partner-hero-title">
            <div class="visual-orb visual-orb-one" aria-hidden="true"></div>
            <div class="visual-orb visual-orb-two" aria-hidden="true"></div>

            <header class="visual-header">
                <a class="brand-mark" href="{{ url('/') }}" aria-label="Hagzz">
                    <img src="{{ asset('assetsAdmin/logo/Primary.svg') }}" alt="">
                    <span>hagzz</span>
                </a>
                <span class="partner-badge">{{ $isArabic ? 'شركاء' : 'Partners' }}</span>
            </header>

            <div class="visual-copy">
                <p class="visual-eyebrow">{{ $copy['eyebrow'] }}</p>
                <h1 id="partner-hero-title">{{ $copy['hero_title'] }}</h1>
                <p class="visual-description">{{ $copy['hero_body'] }}</p>
                <div class="feature-list" aria-label="{{ $copy['eyebrow'] }}">
                    <span><i aria-hidden="true"></i>{{ $copy['feature_students'] }}</span>
                    <span><i aria-hidden="true"></i>{{ $copy['feature_attendance'] }}</span>
                    <span><i aria-hidden="true"></i>{{ $copy['feature_reports'] }}</span>
                </div>
            </div>

            <img class="sports-figure" src="{{ asset('assetsAdmin/sports.png') }}" alt="" aria-hidden="true">

            <div class="visual-insight">
                <span class="insight-icon" aria-hidden="true">↗</span>
                <div>
                    <strong>{{ $copy['card_title'] }}</strong>
                    <p>{{ $copy['card_body'] }}</p>
                </div>
            </div>
        </section>

        <section class="partner-login-form-panel">
            <div class="form-topbar">
                <a class="mobile-brand" href="{{ url('/') }}" aria-label="Hagzz">
                    <img src="{{ asset('assetsAdmin/logo/Primary.svg') }}" alt="">
                    <span>hagzz</span>
                </a>
                <a class="language-link" href="{{ route('lang.switch', $isArabic ? 'en' : 'ar') }}">
                    {{ $copy['language'] }}
                </a>
            </div>

            <div class="login-form-wrap">
                <div class="login-heading">
                    <span class="heading-icon" aria-hidden="true">H</span>
                    <h2>{{ $copy['welcome'] }}</h2>
                    <p>{{ $copy['welcome_body'] }}</p>
                </div>

                @if (session()->has('error'))
                    <div class="login-alert" role="alert">
                        <span aria-hidden="true">!</span>
                        {{ session()->get('error') }}
                    </div>
                @endif

                <form class="partner-login-form" action="{{ route('academy.login') }}" method="post">
                    @csrf
                    <div class="field-group">
                        <label for="partner-email">{{ $copy['email'] }}</label>
                        <input
                            id="partner-email"
                            type="email"
                            name="email"
                            dir="auto"
                            value="{{ old('email') }}"
                            placeholder="{{ $copy['email_placeholder'] }}"
                            autocomplete="email"
                            inputmode="email"
                            required
                            autofocus
                            @class(['has-error' => $errors->has('email')])
                        >
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="partner-password">{{ $copy['password'] }}</label>
                        <div class="password-field">
                            <input
                                id="partner-password"
                                type="password"
                                name="password"
                                dir="auto"
                                placeholder="{{ $copy['password_placeholder'] }}"
                                autocomplete="current-password"
                                required
                                @class(['has-error' => $errors->has('password')])
                            >
                            <button
                                id="password-toggle"
                                type="button"
                                data-show="{{ $copy['show'] }}"
                                data-hide="{{ $copy['hide'] }}"
                                aria-controls="partner-password"
                                aria-pressed="false"
                            >{{ $copy['show'] }}</button>
                        </div>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="remember-choice" for="partner-remember">
                        <input id="partner-remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span aria-hidden="true"></span>
                        {{ $copy['remember'] }}
                    </label>

                    <button class="login-submit" type="submit">
                        <span>{{ $copy['submit'] }}</span>
                        <b aria-hidden="true">{{ $isArabic ? '←' : '→' }}</b>
                    </button>
                </form>

                <div class="login-security">
                    <span aria-hidden="true">✓</span>
                    {{ $copy['security'] }}
                </div>

                <div style="margin-top: 24px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                    <img src="{{ asset('assetsAdmin/logo/mesk_hagzz_logo_dark.png') }}" alt="Mesk | Hagzz" style="height: 38px; max-width: 100%; object-fit: contain; margin-bottom: 12px;">
                    <p style="font-size: 12px; color: #64748b; line-height: 1.6; margin-bottom: 16px;">
                        هذا التطبيق مخصص للشركاء والعملاء المميزين من منصة حجز الرقمية للتكنولوجيا الرياضية من شركة ميسك القطرية
                    </p>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: right; font-size: 13px; color: #1e293b;">
                        <strong style="display: block; font-size: 14px; margin-bottom: 10px; color: #0f172a;">🎧 الدعم الفني والأرقام الرسمية:</strong>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span>🇪🇬 مصر: <strong>01017799580</strong></span>
                            <div>
                                <a href="tel:01017799580" style="padding: 3px 8px; background: #0984e3; color: #fff; border-radius: 6px; text-decoration: none; font-size: 11px; margin-left: 4px;">اتصال 📞</a>
                                <a href="https://wa.me/201017799580" target="_blank" style="padding: 3px 8px; background: #25d366; color: #fff; border-radius: 6px; text-decoration: none; font-size: 11px;">واتساب 💬</a>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span>🇶🇦 قطر: <strong>+97470542458</strong></span>
                            <div>
                                <a href="tel:+97470542458" style="padding: 3px 8px; background: #0984e3; color: #fff; border-radius: 6px; text-decoration: none; font-size: 11px; margin-left: 4px;">اتصال 📞</a>
                                <a href="https://wa.me/97470542458" target="_blank" style="padding: 3px 8px; background: #25d366; color: #fff; border-radius: 6px; text-decoration: none; font-size: 11px;">واتساب 💬</a>
                            </div>
                        </div>

                        <div style="margin-top: 8px; font-size: 12px;">
                            ✉️ البريد: <a href="mailto:info@el7lm.com" style="color: #0984e3; text-decoration: none; font-weight: 600;">info@el7lm.com</a><br>
                            🌐 الموقع الرسمي: <a href="https://hagzz.el7lm.com/" target="_blank" style="color: #0984e3; text-decoration: none; font-weight: 600;">https://hagzz.el7lm.com/</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        (() => {
            const password = document.getElementById('partner-password');
            const toggle = document.getElementById('password-toggle');
            if (password && toggle) {
                toggle.addEventListener('click', () => {
                    const isVisible = password.type === 'text';
                    password.type = isVisible ? 'password' : 'text';
                    toggle.textContent = isVisible ? toggle.dataset.show : toggle.dataset.hide;
                    toggle.setAttribute('aria-pressed', String(!isVisible));
                });
            }

            const form = document.querySelector('.partner-login-form');
            if (form) {
                form.querySelectorAll('input').forEach(input => {
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            if (form.checkValidity()) {
                                const submitBtn = form.querySelector('.login-submit');
                                if (submitBtn) submitBtn.click();
                            }
                        }
                    });
                });
            }
        })();
    </script>
</body>
</html>
