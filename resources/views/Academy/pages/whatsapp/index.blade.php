@extends('Academy.Layouts.master')
@php($ar = app()->getLocale()==='ar')
@section('title', $ar ? 'مركز WhatsApp' : 'WhatsApp centre')
@push('css') @include('Academy.pages.whatsapp._styles') @endpush
@section('content')
<div class="middle-content container-xxl p-0 wa-page">
    <section class="wa-hero"><h1><i class="fa-brands fa-whatsapp"></i> {{ $ar?'مركز WhatsApp':'WhatsApp centre' }}</h1><p>{{ $ar?'محادثات اللاعبين والمدربين والحملات في مكان واحد.':'Player, coach and campaign conversations in one place.' }}</p><div class="wa-toolbar"><a class="wa-btn wa-btn-light" href="{{ route('academy.whatsapp.compose') }}"><i class="fa-solid fa-paper-plane"></i>{{ $ar?'رسالة أو حملة جديدة':'New message or campaign' }}</a><a class="wa-btn wa-btn-light" href="{{ route('academy.whatsapp.settings.edit') }}"><i class="fa-solid fa-link"></i>{{ $ar?'ربط حساب الشريك':'Connect partner account' }}</a></div></section>
    @if(!$channel?->isReady())<div class="wa-alert"><i class="fa-solid fa-triangle-exclamation"></i> {{ $ar?'لم يتم ربط حساب WhatsApp Business الخاص بهذه الأكاديمية بعد. يمكنك تجهيز الرسائل، لكن الإرسال يتطلب إكمال الربط.':'This academy WhatsApp Business account is not connected yet.' }}</div>@endif
    @if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
    <div class="wa-grid">
        <section class="wa-card"><div class="wa-card-head"><h2>{{ $ar?'المحادثات':'Conversations' }}</h2><span class="wa-status">{{ $conversations->total() }}</span></div><div style="padding:12px 16px"><form><input class="wa-search" name="search" value="{{ request('search') }}" placeholder="{{ $ar?'بحث بالاسم أو الرقم':'Search name or phone' }}"></form></div><div class="wa-list">
            @forelse($conversations as $conversation)<a class="wa-conversation" href="{{ route('academy.whatsapp.conversations.show',$conversation) }}"><div class="wa-avatar">{{ mb_substr($conversation->contact_name,0,1) }}</div><div class="wa-conversation-body"><div class="wa-conversation-top"><span class="wa-name">{{ $conversation->contact_name }}</span><span class="wa-time">{{ $conversation->last_message_at?->diffForHumans() }}</span></div><div class="wa-preview">{{ $conversation->phone }}</div></div>@if($conversation->unread_count)<span class="wa-unread">{{ $conversation->unread_count }}</span>@endif</a>@empty<div class="wa-empty"><i class="fa-regular fa-comments"></i><div>{{ $ar?'لا توجد محادثات بعد':'No conversations yet' }}</div></div>@endforelse
        </div><div class="p-3">{{ $conversations->links() }}</div></section>
        <section class="wa-card"><div class="wa-card-head"><h2>{{ $ar?'آخر عمليات الإرسال':'Recent sends' }}</h2><a href="{{ route('academy.whatsapp.compose') }}">{{ $ar?'إرسال جديد':'New send' }}</a></div>
            @forelse($campaigns as $campaign)<div class="wa-campaign"><div><strong>{{ $campaign->name }}</strong><span class="wa-preview">{{ $campaign->created_at->format('Y-m-d H:i') }} · {{ $campaign->message_type }}</span></div><div style="text-align:end"><span class="wa-status">{{ $campaign->status }}</span><div class="wa-preview mt-1">{{ $campaign->sent_count }}/{{ $campaign->total_recipients }} · {{ $campaign->failed_count }} {{ $ar?'فشل':'failed' }}</div></div></div>@empty<div class="wa-empty"><i class="fa-solid fa-bullhorn"></i><div>{{ $ar?'لا توجد حملات مرسلة':'No campaigns sent' }}</div></div>@endforelse
        </section>
    </div>
</div>
@endsection
