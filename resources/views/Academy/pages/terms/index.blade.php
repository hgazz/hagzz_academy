@extends('Academy.Layouts.master')

@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = $isArabic ? [
        'title' => 'الشروط والأحكام',
        'eyebrow' => 'مركز السياسات',
        'intro' => 'راجع الأحكام المنظمة لاستخدام منصة Hagzz والخدمات المقدمة إلى الشركاء.',
        'official' => 'وثيقة رسمية',
        'updated' => 'آخر تحديث',
        'contents' => 'محتويات الوثيقة',
        'contentsHint' => 'اختر عنوانًا للانتقال إليه مباشرة',
        'print' => 'طباعة الوثيقة',
        'top' => 'العودة للأعلى',
        'emptyTitle' => 'لم تتم إضافة الشروط بعد',
        'emptyText' => 'ستظهر الشروط والأحكام هنا بعد اعتمادها من إدارة المنصة.',
        'section' => 'الشروط كاملة',
    ] : [
        'title' => 'Terms & conditions',
        'eyebrow' => 'Policy centre',
        'intro' => 'Review the terms governing the use of Hagzz and the services provided to partners.',
        'official' => 'Official document',
        'updated' => 'Last updated',
        'contents' => 'On this page',
        'contentsHint' => 'Choose a heading to jump to it',
        'print' => 'Print document',
        'top' => 'Back to top',
        'emptyTitle' => 'No terms have been published yet',
        'emptyText' => 'The terms and conditions will appear here after they are approved by the platform.',
        'section' => 'Full terms',
    ];
    $updatedLabel = $termsUpdatedAt
        ? $termsUpdatedAt->locale($isArabic ? 'ar' : 'en')->translatedFormat('d F Y')
        : ($isArabic ? 'غير محدد' : 'Not specified');
@endphp

@section('title', $copy['title'])

@push('css')
<style>
    .terms-page { --terms-primary: #3157d5; --terms-ink: #16213e; --terms-muted: #64748b; --terms-line: #e5eaf3; position: relative; padding-bottom: 44px; }
    .terms-progress { position: fixed; inset-inline-start: 0; top: 0; z-index: 1100; width: 0; height: 3px; background: linear-gradient(90deg, #3157d5, #7c3aed, #06b6d4); transition: width .08s linear; }
    .terms-hero { position: relative; overflow: hidden; margin-top: 18px; padding: clamp(28px, 5vw, 58px); border-radius: 26px; color: #fff; background: linear-gradient(125deg, #172554 0%, #3157d5 54%, #6d28d9 100%); box-shadow: 0 24px 60px rgba(49, 87, 213, .22); }
    .terms-hero::before, .terms-hero::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); }
    .terms-hero::before { width: 290px; height: 290px; inset-inline-end: -80px; top: -150px; }
    .terms-hero::after { width: 170px; height: 170px; inset-inline-end: 18%; bottom: -115px; }
    .terms-hero-content { position: relative; z-index: 2; max-width: 820px; }
    .terms-eyebrow { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; padding: 8px 12px; border: 1px solid rgba(255,255,255,.24); border-radius: 999px; background: rgba(255,255,255,.1); font-size: 12px; font-weight: 800; letter-spacing: .04em; }
    .terms-hero h1 { color: #fff; margin: 0 0 12px; font-size: clamp(30px, 4.4vw, 52px); font-weight: 900; line-height: 1.15; }
    .terms-hero p { max-width: 680px; margin: 0; color: rgba(255,255,255,.8); font-size: 16px; line-height: 1.9; }
    .terms-meta { position: relative; z-index: 2; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 25px; }
    .terms-meta-item { display: inline-flex; align-items: center; gap: 8px; padding: 10px 13px; border-radius: 12px; background: rgba(255,255,255,.12); color: #fff; font-size: 13px; font-weight: 700; }
    .terms-print { display: inline-flex; align-items: center; gap: 8px; padding: 10px 15px; border: 0; border-radius: 12px; background: #fff; color: #2445b8; font-weight: 800; cursor: pointer; transition: transform .18s ease, box-shadow .18s ease; }
    .terms-print:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(3,7,18,.18); }
    .terms-layout { display: grid; grid-template-columns: minmax(0, 1fr); gap: 24px; margin-top: 28px; align-items: start; }
    .terms-layout.has-toc { grid-template-columns: minmax(220px, 280px) minmax(0, 1fr); }
    .terms-toc { position: sticky; top: 96px; padding: 20px; border: 1px solid var(--terms-line); border-radius: 20px; background: #fff; box-shadow: 0 12px 38px rgba(30, 41, 59, .07); }
    .terms-toc-icon { width: 42px; height: 42px; display: grid; place-items: center; margin-bottom: 14px; border-radius: 13px; color: var(--terms-primary); background: #eef2ff; }
    .terms-toc h2 { margin: 0 0 5px; color: var(--terms-ink); font-size: 17px; font-weight: 900; }
    .terms-toc > p { margin: 0 0 16px; color: var(--terms-muted); font-size: 12px; line-height: 1.7; }
    .terms-toc-list { display: grid; gap: 5px; max-height: calc(100vh - 310px); overflow: auto; padding-inline-end: 4px; }
    .terms-toc-link { display: block; padding: 9px 11px; border-inline-start: 3px solid transparent; border-radius: 8px; color: #536079; font-size: 13px; line-height: 1.55; text-decoration: none; transition: .18s ease; }
    .terms-toc-link:hover, .terms-toc-link.active { color: var(--terms-primary); border-inline-start-color: var(--terms-primary); background: #f4f6ff; }
    .terms-toc-link.toc-h3 { padding-inline-start: 24px; font-size: 12px; }
    .terms-document { min-width: 0; padding: clamp(24px, 4vw, 50px); border: 1px solid var(--terms-line); border-radius: 24px; background: #fff; box-shadow: 0 16px 50px rgba(30, 41, 59, .08); }
    .terms-document-header { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; padding-bottom: 22px; border-bottom: 1px solid var(--terms-line); }
    .terms-document-header .icon { width: 48px; height: 48px; display: grid; place-items: center; flex: 0 0 48px; border-radius: 15px; color: #fff; background: linear-gradient(135deg, #3157d5, #7c3aed); box-shadow: 0 9px 22px rgba(49,87,213,.22); }
    .terms-document-header h2 { margin: 0; color: var(--terms-ink); font-size: 22px; font-weight: 900; }
    .terms-document-header p { margin: 4px 0 0; color: var(--terms-muted); font-size: 13px; }
    .terms-content { color: #3d4960; font-size: 15px; line-height: 2; overflow-wrap: anywhere; }
    .terms-content h1, .terms-content h2, .terms-content h3, .terms-content h4 { scroll-margin-top: 110px; color: var(--terms-ink); font-weight: 900; line-height: 1.45; }
    .terms-content h1 { margin: 0 0 24px; font-size: 27px; }
    .terms-content h2 { margin: 38px 0 14px; padding-bottom: 10px; border-bottom: 1px solid var(--terms-line); font-size: 22px; }
    .terms-content h3 { margin: 28px 0 10px; font-size: 18px; }
    .terms-content p { margin: 0 0 16px; }
    .terms-content ul, .terms-content ol { margin: 0 0 20px; padding-inline-start: 24px; }
    .terms-content li { margin-bottom: 8px; padding-inline-start: 5px; }
    .terms-content li::marker { color: var(--terms-primary); font-weight: 800; }
    .terms-content a { color: var(--terms-primary); font-weight: 700; text-decoration: underline; text-underline-offset: 3px; }
    .terms-content blockquote { margin: 24px 0; padding: 18px 20px; border-inline-start: 4px solid var(--terms-primary); border-radius: 12px; background: #f5f7ff; color: #35415a; }
    .terms-content table { width: 100%; margin: 24px 0; border-collapse: separate; border-spacing: 0; overflow: hidden; border: 1px solid var(--terms-line); border-radius: 14px; }
    .terms-content th, .terms-content td { padding: 13px 15px; border-bottom: 1px solid var(--terms-line); text-align: start; }
    .terms-content th { color: var(--terms-ink); background: #f6f8fc; font-weight: 800; }
    .terms-empty { padding: 60px 20px; text-align: center; }
    .terms-empty i { width: 72px; height: 72px; display: grid; place-items: center; margin: 0 auto 18px; border-radius: 22px; color: var(--terms-primary); background: #eef2ff; font-size: 28px; }
    .terms-empty h3 { margin-bottom: 8px; color: var(--terms-ink); font-weight: 900; }
    .terms-empty p { color: var(--terms-muted); }
    .terms-back-top { position: fixed; inset-inline-end: 28px; bottom: 28px; z-index: 30; width: 44px; height: 44px; display: grid; place-items: center; border: 0; border-radius: 14px; color: #fff; background: var(--terms-primary); box-shadow: 0 12px 28px rgba(49,87,213,.3); opacity: 0; pointer-events: none; transform: translateY(8px); transition: .2s ease; }
    .terms-back-top.show { opacity: 1; pointer-events: auto; transform: none; }
    body.dark .terms-page { --terms-ink: #edf2ff; --terms-muted: #a8b3ca; --terms-line: #2a354c; }
    body.dark .terms-toc, body.dark .terms-document { background: #172033; }
    body.dark .terms-toc-link:hover, body.dark .terms-toc-link.active, body.dark .terms-content blockquote { background: #202c46; }
    body.dark .terms-content { color: #c2cbe0; }
    body.dark .terms-content th { background: #202a3e; }
    @media (max-width: 991px) { .terms-layout.has-toc { grid-template-columns: 1fr; } .terms-toc { position: relative; top: 0; } .terms-toc-list { max-height: 230px; } }
    @media (max-width: 575px) { .terms-hero { border-radius: 20px; } .terms-meta { align-items: stretch; } .terms-meta-item, .terms-print { width: 100%; justify-content: center; } .terms-document { border-radius: 18px; padding: 22px 18px; } .terms-back-top { inset-inline-end: 16px; bottom: 16px; } }
    @media print { .sidebar-wrapper, .header-container, .secondary-nav, .terms-toc, .terms-print, .terms-back-top, .terms-progress { display: none !important; } .main-container, #content, .middle-content { margin: 0 !important; padding: 0 !important; width: 100% !important; } .terms-hero { color: #111; background: #fff !important; box-shadow: none; border: 1px solid #ddd; } .terms-hero h1, .terms-hero p { color: #111; } .terms-meta-item { color: #111; border: 1px solid #ddd; } .terms-layout { display: block; } .terms-document { border: 0; box-shadow: none; padding: 24px 0; } }
</style>
@endpush

@section('content')
<div class="middle-content container-xxl p-0 terms-page" id="terms-page">
    <div class="terms-progress" id="terms-progress" aria-hidden="true"></div>

    <div class="secondary-nav">
        <div class="breadcrumbs-container" data-page-heading="{{ $copy['title'] }}">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" aria-label="Menu"><i class="fa-solid fa-bars"></i></a>
                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li><li class="breadcrumb-item active" aria-current="page">{{ $copy['title'] }}</li></ol></nav>
            </header>
        </div>
    </div>

    <section class="terms-hero">
        <div class="terms-hero-content">
            <span class="terms-eyebrow"><i class="fa-solid fa-shield-halved"></i>{{ $copy['eyebrow'] }}</span>
            <h1>{{ $copy['title'] }}</h1>
            <p>{{ $copy['intro'] }}</p>
        </div>
        <div class="terms-meta">
            <span class="terms-meta-item"><i class="fa-solid fa-circle-check"></i>{{ $copy['official'] }}</span>
            <span class="terms-meta-item"><i class="fa-regular fa-calendar"></i>{{ $copy['updated'] }}: {{ $updatedLabel }}</span>
            <button type="button" class="terms-print" onclick="window.print()"><i class="fa-solid fa-print"></i>{{ $copy['print'] }}</button>
        </div>
    </section>

    <div class="terms-layout">
        <aside class="terms-toc" id="terms-toc" hidden>
            <div class="terms-toc-icon"><i class="fa-solid fa-list-ul"></i></div>
            <h2>{{ $copy['contents'] }}</h2>
            <p>{{ $copy['contentsHint'] }}</p>
            <nav class="terms-toc-list" id="terms-toc-list" aria-label="{{ $copy['contents'] }}"></nav>
        </aside>

        <article class="terms-document">
            <header class="terms-document-header">
                <div class="icon"><i class="fa-solid fa-file-contract"></i></div>
                <div><h2>{{ $copy['section'] }}</h2><p>{{ $copy['updated'] }}: {{ $updatedLabel }}</p></div>
            </header>
            @if(trim(strip_tags($terms)) !== '')
                <div class="terms-content" id="terms-content" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">{!! $terms !!}</div>
            @else
                <div class="terms-empty"><i class="fa-solid fa-file-circle-exclamation"></i><h3>{{ $copy['emptyTitle'] }}</h3><p>{{ $copy['emptyText'] }}</p></div>
            @endif
        </article>
    </div>

    <button type="button" class="terms-back-top" id="terms-back-top" title="{{ $copy['top'] }}" aria-label="{{ $copy['top'] }}"><i class="fa-solid fa-arrow-up"></i></button>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const page = document.getElementById('terms-page');
    const content = document.getElementById('terms-content');
    const toc = document.getElementById('terms-toc');
    const tocList = document.getElementById('terms-toc-list');
    const progress = document.getElementById('terms-progress');
    const backTop = document.getElementById('terms-back-top');
    if (!page) return;

    const slugify = function (text, index) {
        const slug = text.trim().toLowerCase().replace(/[^\p{L}\p{N}]+/gu, '-').replace(/^-|-$/g, '');
        return 'term-' + (slug || index + 1);
    };

    if (content) {
        const headings = Array.from(content.querySelectorAll('h1, h2, h3, h4'));
        const usedIds = new Set();
        headings.forEach(function (heading, index) {
            let id = heading.id || slugify(heading.textContent, index);
            while (usedIds.has(id) || document.getElementById(id)) id += '-' + (index + 1);
            usedIds.add(id);
            heading.id = id;
            const link = document.createElement('a');
            link.href = '#' + id;
            link.className = 'terms-toc-link ' + (/H[34]/.test(heading.tagName) ? 'toc-h3' : 'toc-h2');
            link.textContent = heading.textContent.trim();
            link.addEventListener('click', function (event) { event.preventDefault(); heading.scrollIntoView({ behavior: 'smooth', block: 'start' }); history.replaceState(null, '', '#' + id); });
            tocList.appendChild(link);
        });
        if (headings.length) {
            toc.hidden = false;
            toc.closest('.terms-layout').classList.add('has-toc');
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    tocList.querySelectorAll('.active').forEach(function (item) { item.classList.remove('active'); });
                    const active = tocList.querySelector('a[href="#' + CSS.escape(entry.target.id) + '"]');
                    if (active) { active.classList.add('active'); active.scrollIntoView({ block: 'nearest' }); }
                });
            }, { rootMargin: '-18% 0px -68% 0px', threshold: 0 });
            headings.forEach(function (heading) { observer.observe(heading); });
        }
    }

    const updateReadingState = function () {
        const start = page.offsetTop;
        const available = Math.max(1, page.scrollHeight - window.innerHeight);
        const value = Math.min(100, Math.max(0, ((window.scrollY - start) / available) * 100));
        progress.style.width = value + '%';
        backTop.classList.toggle('show', window.scrollY > 520);
    };
    window.addEventListener('scroll', updateReadingState, { passive: true });
    window.addEventListener('resize', updateReadingState, { passive: true });
    backTop.addEventListener('click', function () { page.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
    updateReadingState();
});
</script>
@endpush
