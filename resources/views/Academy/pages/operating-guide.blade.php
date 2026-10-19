@extends('Academy.Layouts.master')

@section('title', trans('partner_workflow.title'))

@push('css')
<style>
    .partner-operating-guide { --guide-border: #e3e8f1; --guide-muted: #65718a; --guide-card: #fff; color: #192b4b; padding-block: 18px 40px; }
    .partner-operating-guide .guide-hero { padding: clamp(22px, 4vw, 40px); border: 1px solid #d9e4fb; border-radius: 20px; background: linear-gradient(120deg, #edf4ff, #fff); margin-bottom: 20px; }
    .partner-operating-guide .guide-eyebrow { font-size: 12px; font-weight: 700; color: #2458cb; margin-bottom: 10px; }
    .partner-operating-guide h1 { font-size: clamp(25px, 3vw, 36px); font-weight: 800; margin: 8px 0 14px; color: inherit; }
    .partner-operating-guide h2 { font-size: 20px; font-weight: 750; color: inherit; margin: 0 0 10px; }
    .partner-operating-guide h3 { font-size: 16px; font-weight: 700; color: inherit; margin: 0; }
    .partner-operating-guide p { color: var(--guide-muted); line-height: 1.95; font-size: 14px; margin: 0; }
    .partner-operating-guide .guide-intro { max-width: 850px; margin-bottom: 20px; }
    .partner-operating-guide .guide-type, .partner-operating-guide .guide-lane { display: inline-flex; padding: 5px 12px; border-radius: 20px; background: #e4edff; color: #2458cb; font-size: 12px; font-weight: 700; }
    .partner-operating-guide .guide-start { display: inline-flex; padding: 10px 18px; margin-top: 18px; border-radius: 10px; background: #2458cb; color: #fff; font-weight: 700; }
    .partner-operating-guide .guide-notice { padding: 14px 18px; margin-bottom: 18px; border-inline-start: 3px solid #4d7ce7; background: #edf3ff; border-radius: 10px; }
    .partner-operating-guide .guide-notice p { color: #34547f; }
    .partner-operating-guide .guide-paths { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 14px; margin: 16px 0 26px; }
    .partner-operating-guide .guide-path { padding: 22px; background: var(--guide-card); border: 1px solid var(--guide-border); border-radius: 14px; }
    .partner-operating-guide .guide-path p { margin-top: 10px; font-size: 13px; }
    .partner-operating-guide .guide-path ol { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 18px 0 0; }
    .partner-operating-guide .guide-path li { display: flex; align-items: center; gap: 8px; }
    .partner-operating-guide .guide-path li:not(:last-child)::after { content: '←'; color: #8b9ab5; }
    [dir="ltr"] .partner-operating-guide .guide-path li:not(:last-child)::after { content: '→'; }
    .partner-operating-guide .guide-path a { padding: 6px 10px; border-radius: 7px; border: 1px solid var(--guide-border); color: #2458cb; font-size: 12px; line-height: 1.7; }
    .partner-operating-guide a:focus-visible { outline: 2px solid #2458cb; outline-offset: 3px; }
    .partner-operating-guide .guide-concepts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr)); gap: 12px; margin: 16px 0 28px; }
    .partner-operating-guide .guide-concept { border: 1px dashed #cbd7eb; border-radius: 12px; padding: 18px; }
    .partner-operating-guide .guide-concept p { font-size: 13px; margin-top: 8px; }
    .partner-operating-guide .guide-stage { background: var(--guide-card); border: 1px solid var(--guide-border); border-radius: 16px; padding: clamp(16px, 3vw, 28px); margin-top: 18px; scroll-margin-top: 100px; }
    .partner-operating-guide .guide-stage-heading { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 20px; }
    .partner-operating-guide .guide-stage-number { flex: 0 0 36px; height: 36px; display: grid; place-items: center; background: #eaf0fc; color: #2458cb; border-radius: 10px; font-weight: 800; }
    .partner-operating-guide .guide-step { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; padding: 20px 0; border-top: 1px solid var(--guide-border); scroll-margin-top: 100px; }
    .partner-operating-guide .guide-step-title { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; margin-bottom: 9px; }
    .partner-operating-guide .guide-step-title i { color: #5578bc; width: 20px; text-align: center; }
    .partner-operating-guide .guide-step-link { display: inline-flex; align-self: start; white-space: nowrap; padding: 8px 13px; border: 1px solid #d4dff4; border-radius: 8px; color: #2458cb; font-size: 13px; }
    .partner-operating-guide .guide-step-link:hover, .partner-operating-guide .guide-path a:hover { background: #edf3ff; }
    .partner-operating-guide .guide-lane { font-size: 10px; margin-inline-start: 8px; }
    body.dark .partner-operating-guide, .dark .partner-operating-guide { --guide-border: #30415f; --guide-muted: #b6c2d6; --guide-card: #17263f; color: #e4ebf9; }
    body.dark .partner-operating-guide .guide-hero, .dark .partner-operating-guide .guide-hero { background: #1a2d4c; border-color: #304b74; }
    body.dark .partner-operating-guide .guide-notice, .dark .partner-operating-guide .guide-notice { background: #213858; }
    body.dark .partner-operating-guide .guide-notice p, .dark .partner-operating-guide .guide-notice p { color: #c0d3ef; }
    body.dark .partner-operating-guide .guide-step-link, body.dark .partner-operating-guide .guide-path a, .dark .partner-operating-guide .guide-step-link, .dark .partner-operating-guide .guide-path a { color: #a7c5ff; }
    body.dark .partner-operating-guide .guide-eyebrow, .dark .partner-operating-guide .guide-eyebrow { color: #a7c5ff; }
    body.dark .partner-operating-guide .guide-step-link:hover, body.dark .partner-operating-guide .guide-path a:hover, .dark .partner-operating-guide .guide-step-link:hover, .dark .partner-operating-guide .guide-path a:hover { background: #233c60; }
    @media (max-width: 575px) { .partner-operating-guide .guide-step { grid-template-columns: 1fr; } .partner-operating-guide .guide-step-link { justify-self: start; } }
</style>
@endpush

@section('content')
@php
    $guideLocale = session('locale', app()->getLocale());
    $text = fn(string $key) => trans('partner_workflow.' . $key, [], $guideLocale);
    $firstPage = $workspace['sections'][0]['items'][0] ?? null;
@endphp
<div class="partner-operating-guide" dir="{{ $guideLocale === 'ar' ? 'rtl' : 'ltr' }}">
    <header class="guide-hero">
        <div class="guide-eyebrow">{{ $text('eyebrow') }}</div>
        <span class="guide-type">{{ $text('types.' . $workspace['type']) }}</span>
        <h1>{{ $text('title') }}</h1>
        <p class="guide-intro">{{ $text('intro') }}</p>
        <p>{{ $text('permissions_note') }}</p>
        @if($firstPage)
            <a class="guide-start" data-no-loader="true" href="#guide-page-{{ $firstPage['id'] }}">{{ $text('start') }}</a>
        @endif
    </header>

    @if(in_array($workspace['type'], ['venue', 'hybrid']) && !$workspace['venueEnabled'])
        <aside class="guide-notice"><p>{{ $text('venue_locked') }}</p></aside>
    @endif
    @if($workspace['type'] === 'hybrid')
        <aside class="guide-notice"><p>{{ $text('hybrid_note') }}</p></aside>
    @endif

    @if($workspace['paths'])
        <h2>{{ $text('paths_heading') }}</h2>
        <div class="guide-paths">
            @foreach($workspace['paths'] as $path)
                <section class="guide-path" data-path="{{ $path['id'] }}">
                    <h3>{{ $text('paths.' . $path['id']) }}</h3>
                    <p>{{ $text('path_notes.' . $path['id']) }}</p>
                    <ol>
                        @foreach($path['items'] as $page)
                            <li><a data-no-loader="true" href="#guide-page-{{ $page['id'] }}">{{ trans($page['title'], [], $guideLocale) }}</a></li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>
    @endif

    <h2>{{ $text('concepts_title') }}</h2>
    <div class="guide-concepts">
        @if($workspace['type'] !== 'venue')
            @php($concepts = in_array($workspace['type'], ['gym', 'health_center']) ? ['membership', 'program'] : ['program', 'group'])
            @foreach($concepts as $concept)
                <section class="guide-concept"><h3>{{ $text('concepts.' . $concept . '_title') }}</h3><p>@if($concept === 'program' && in_array($workspace['type'], ['gym', 'health_center'])){{ $text('optional') }}: @endif{{ $text('concepts.' . $concept) }}</p></section>
            @endforeach
        @endif
        @if(in_array($workspace['type'], ['venue', 'hybrid']))
            <section class="guide-concept"><h3>{{ $text('concepts.venue_title') }}</h3><p>{{ $text('concepts.venue') }}</p></section>
        @endif
    </div>

    <h2>{{ $text('steps_heading') }}</h2>
    @forelse($workspace['sections'] as $section)
        <section class="guide-stage" id="guide-{{ $section['id'] }}" data-stage="{{ $section['id'] }}">
            <div class="guide-stage-heading">
                <span class="guide-stage-number" aria-hidden="true">{{ $loop->iteration }}</span>
                <div><h2>{{ $text('sections.' . $section['id']) }}</h2><p>{{ $text('section_notes.' . $section['id']) }}</p></div>
            </div>
            @foreach($section['items'] as $page)
                <article class="guide-step" id="guide-page-{{ $page['id'] }}" data-page="{{ $page['id'] }}">
                    <div>
                        <div class="guide-step-title">
                            <i class="fa-solid fa-{{ $page['icon'] }}" aria-hidden="true"></i>
                            <h3>{{ trans($page['title'], [], $guideLocale) }}</h3>
                            @if($workspace['type'] === 'hybrid' && $page['lane'] !== 'shared')
                                <span class="guide-lane">{{ $text('lanes.' . $page['lane']) }}</span>
                            @endif
                        </div>
                        <p>{{ $text('steps.' . $page['id']) }}</p>
                    </div>
                    <a class="guide-step-link" href="{{ route($page['route']) }}" @if($page['newTab'] ?? false) target="_blank" rel="noopener noreferrer" @endif>{{ $text('open_page') }}<span class="visually-hidden">: {{ trans($page['title'], [], $guideLocale) }}</span></a>
                </article>
            @endforeach
        </section>
    @empty
        <p>{{ $text('empty') }}</p>
    @endforelse
</div>
@endsection
