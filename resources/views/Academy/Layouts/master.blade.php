<!DOCTYPE html>
@php $__locale = session('locale', app()->getLocale()); @endphp
<html lang="{{ $__locale }}" dir="{{ $__locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assetsAdmin/logo/Primary.svg') }}"/>
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}; url={{ route('academy.logout') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="lang" content="{{ $__locale }}" />
        @include('Academy.Layouts.inc.head')
    <style>
        /* ─── CORE LAYOUT FIX ──────────────────────────────────────────────
         * The template sets body { height: 100% } which means the body
         * is capped at the viewport height. Any content taller than the
         * viewport is clipped (hidden behind the Windows taskbar / browser
         * chrome bottom edge) unless scrolling is properly set up.
         *
         * Fix: switch body to min-height so it grows with its content,
         * and make html + body scrollable.
         * ─────────────────────────────────────────────────────────────────*/

        html, body {
            height: auto !important;
            min-height: 100vh !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        /* The main container must also expand with content */
        .main-container {
            min-height: 100vh;
            height: auto !important;
        }

        /* .secondary-nav: keep it in the document flow (not fixed)
           so content below it is NOT hidden behind it */
        .secondary-nav {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            width: 100% !important;
            z-index: auto !important;
            background: #fafafa;
            box-shadow: 0 4px 6px -2px rgba(126,142,177,.10);
            min-height: 52px;
            display: flex;
            margin-bottom: 16px;
        }

        /* #content: was margin-top:107px (navbar+secondary-nav both fixed).
           Now secondary-nav is in-flow, only need navbar height as offset. */
        #content.main-content {
            margin-top: 60px !important;
            height: auto !important;
        }

        /* Remove the forced min-height that was causing overflow */
        .layout-px-spacing {
            min-height: auto !important;
            padding-bottom: 48px !important;
        }

        /* Footer must always be fully visible */
        .footer-wrapper {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 767.98px) {
            .layout-px-spacing {
                padding-bottom: 64px !important;
            }
        }
    </style>
</head>
<body class="layout-boxed">
<!-- BEGIN LOADER -->
<div id="load_screen">
    <div class="loader">
        <div class="loader-content">
            <div class="hagzz-loader-scene" aria-hidden="true">
                <div class="hagzz-loader-orbit">
                    <span class="hagzz-ball hagzz-ball-football"></span>
                    <span class="hagzz-ball hagzz-ball-basketball"></span>
                    <span class="hagzz-ball hagzz-ball-tennis"></span>
                </div>
                <div class="hagzz-loader-mark">H</div>
            </div>
            <div class="hagzz-loader-copy">
                <strong>{{ $__locale === 'ar' ? 'منظومة حجز الرقمية' : 'Hagzz Digital Platform' }}</strong>
                <span>
                    {{ $__locale === 'ar' ? 'نجهز لوحة التحكم' : 'Preparing your dashboard' }}
                    <span class="hagzz-loader-dots"><i></i><i></i><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>
<!--  END LOADER -->

<!--  BEGIN NAVBAR  -->
@include('Academy.Layouts.inc.navbar')
<!--  END NAVBAR  -->

<!--  BEGIN MAIN CONTAINER  -->
<div class="main-container" id="container">

    <div class="overlay"></div>
    <div class="search-overlay"></div>

    <!--  BEGIN SIDEBAR  -->
    @include('Academy.Layouts.inc.sidebar')
    <!--  END SIDEBAR  -->

    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            @include('Academy.Layouts.inc.subscription-alert')
            @yield('content')

        </div>
        <!--  BEGIN FOOTER  -->
        <div class="footer-wrapper">
            <div class="footer-section f-section-1">
                <p class="">Copyright © <span class="dynamic-year">2024</span> <a target="_blank" href="https://hagzz.com">Hagzz</a>, All rights reserved.</p>
            </div>
        </div>
        <!--  END FOOTER  -->
    </div>
    <!--  END CONTENT AREA  -->

</div>
<!-- END MAIN CONTAINER -->

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
@include('Academy.Layouts.inc.footerJs')
<!-- END GLOBAL MANDATORY SCRIPTS -->
</body>
</html>
