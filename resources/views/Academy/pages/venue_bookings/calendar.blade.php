@extends('Academy.Layouts.master')
@section('title', app()->getLocale() === 'ar' ? 'تقويم حجوزات الملاعب' : 'Venue booking calendar')

@push('css')
    <link href="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">
    <style>
        .venue-calendar-page { --ink:#102a43; --muted:#64748b; --line:#e2e8f0; --brand:#0f766e; }
        .venue-calendar-head { display:flex; align-items:center; justify-content:space-between; gap:18px; margin:22px 0; }
        .venue-calendar-head h2 { margin:0 0 5px; color:var(--ink); font-size:25px; }
        .venue-calendar-head p { margin:0; color:var(--muted); }
        .venue-calendar-actions { display:flex; gap:9px; flex-wrap:wrap; }
        .venue-action { display:inline-flex; align-items:center; gap:7px; min-height:42px; padding:9px 14px; border:1px solid var(--line); border-radius:7px; background:#fff; color:var(--ink); font-weight:700; }
        .venue-action.primary { color:#fff; background:var(--brand); border-color:var(--brand); }
        .venue-action svg { width:17px; height:17px; }
        .venue-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; margin-bottom:16px; }
        .venue-metric { display:flex; align-items:center; gap:13px; min-height:92px; padding:16px; background:#fff; border:1px solid var(--line); border-radius:8px; }
        .venue-metric i { display:grid; place-items:center; width:42px; height:42px; flex:0 0 42px; border-radius:8px; color:#0f766e; background:#ccfbf1; }
        .venue-metric:nth-child(2) i { color:#1d4ed8; background:#dbeafe; } .venue-metric:nth-child(3) i { color:#7c3aed; background:#ede9fe; } .venue-metric:nth-child(4) i { color:#b45309; background:#fef3c7; }
        .venue-metric svg { width:21px; height:21px; } .venue-metric span { display:block; color:var(--muted); font-size:12px; } .venue-metric strong { color:var(--ink); font-size:22px; }
        .venue-calendar-shell { display:grid; grid-template-columns:260px minmax(0,1fr); border:1px solid var(--line); border-radius:8px; background:#fff; overflow:hidden; }
        .venue-calendar-side { padding:18px; border-inline-end:1px solid var(--line); background:#f8fafc; }
        .venue-calendar-side h4 { color:var(--ink); font-size:15px; margin:0 0 13px; }
        .venue-filter { margin-bottom:14px; } .venue-filter label { display:block; color:var(--muted); font-size:12px; font-weight:700; margin-bottom:6px; }
        .venue-filter select { width:100%; min-height:40px; border:1px solid var(--line); border-radius:6px; background:#fff; padding:7px 10px; }
        .venue-legend { display:grid; gap:8px; margin-top:20px; } .venue-legend span { display:flex; align-items:center; gap:8px; color:#475569; font-size:12px; }
        .venue-legend i { width:9px; height:9px; border-radius:50%; }
        .venue-today-list { margin-top:22px; } .venue-today-item { display:block; padding:10px; margin-bottom:8px; border:1px solid var(--line); border-radius:7px; background:#fff; color:var(--ink); }
        .venue-today-item strong,.venue-today-item small { display:block; } .venue-today-item small { color:var(--muted); margin-top:3px; }
        .venue-calendar-main { min-width:0; padding:18px; }
        .venue-calendar-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:15px; flex-wrap:wrap; }
        .venue-calendar-nav,.venue-calendar-views { display:flex; align-items:center; gap:5px; }
        .venue-calendar-toolbar button { min-height:38px; border:1px solid var(--line); background:#fff; color:#334155; border-radius:6px; padding:7px 11px; }
        .venue-calendar-toolbar button.is-active { color:#fff; background:var(--brand); border-color:var(--brand); }
        #venueCalendarTitle { color:var(--ink); font-size:18px; margin:0; }
        #venueBookingCalendar .fc-event { border-radius:5px; padding:2px; cursor:pointer; } #venueBookingCalendar .fc-event-title { font-weight:700; }
        .booking-drawer-backdrop { position:fixed; inset:0; z-index:1040; background:rgba(15,23,42,.42); opacity:0; visibility:hidden; transition:.2s; }
        .booking-drawer { position:fixed; z-index:1041; top:0; right:0; bottom:0; left:auto; width:min(390px,92vw); padding:24px; overflow-y:auto; background:#fff; transform:translateX(110%); visibility:hidden; pointer-events:none; transition:transform .25s, visibility 0s linear .25s; box-shadow:0 0 30px rgba(15,23,42,.18); }
        .booking-drawer.is-open { transform:translateX(0); visibility:visible; pointer-events:auto; transition:transform .25s; } .booking-drawer-backdrop.is-open { opacity:1; visibility:visible; }
        .booking-drawer header { display:flex; justify-content:space-between; gap:15px; } .booking-drawer h3 { color:var(--ink); } .drawer-close { border:0; background:#f1f5f9; border-radius:6px; width:38px; height:38px; }
        .booking-drawer dl { display:grid; gap:12px; margin:22px 0; } .booking-drawer dl div { padding:12px; border:1px solid var(--line); border-radius:7px; } .booking-drawer dt { color:var(--muted); font-size:12px; } .booking-drawer dd { margin:4px 0 0; color:var(--ink); font-weight:700; }
        @media(max-width:1100px){.venue-metrics{grid-template-columns:repeat(2,1fr)}.venue-calendar-shell{grid-template-columns:1fr}.venue-calendar-side{border-inline-end:0;border-bottom:1px solid var(--line)}}
        @media(max-width:767px){.venue-calendar-head{align-items:flex-start;flex-direction:column}.venue-calendar-actions{width:100%}.venue-action{flex:1;justify-content:center}.venue-metrics{grid-template-columns:1fr}.venue-calendar-main{padding:10px}.venue-calendar-toolbar{align-items:stretch;flex-direction:column}.venue-calendar-nav,.venue-calendar-views{justify-content:center;flex-wrap:wrap}#venueCalendarTitle{text-align:center}.venue-calendar-side{padding:14px}}
    </style>
@endpush

@section('content')
@php
    $ar = app()->getLocale() === 'ar';
    $statusLabels = ['pending'=>$ar?'قيد الانتظار':'Pending','confirmed'=>$ar?'مؤكد':'Confirmed','checked_in'=>$ar?'حضر':'Checked in','completed'=>$ar?'مكتمل':'Completed','cancelled'=>$ar?'ملغي':'Cancelled','no_show'=>$ar?'لم يحضر':'No show'];
@endphp
<div class="middle-content container-xxl p-0 venue-calendar-page" dir="{{ $ar ? 'rtl' : 'ltr' }}">
    <header class="venue-calendar-head">
        <div><h2>{{ $ar ? 'تقويم حجوزات الملاعب' : 'Venue booking calendar' }}</h2><p>{{ $ar ? 'تابع الإشغال والحجوزات والمدفوعات حسب الوقت والمساحة.' : 'Track occupancy, bookings and payments by time and space.' }}</p></div>
        <div class="venue-calendar-actions">
            <a class="venue-action" href="{{ route('academy.venue-bookings.index') }}"><i data-feather="list"></i>{{ $ar ? 'قائمة الحجوزات' : 'Booking list' }}</a>
            <a class="venue-action primary" href="{{ route('academy.venue-bookings.create') }}"><i data-feather="plus"></i>{{ $ar ? 'حجز جديد' : 'New booking' }}</a>
        </div>
    </header>

    <section class="venue-metrics">
        <article class="venue-metric"><i><span data-feather="calendar"></span></i><div><span>{{ $ar ? 'حجوزات اليوم' : 'Today bookings' }}</span><strong>{{ number_format($summary['today']) }}</strong></div></article>
        <article class="venue-metric"><i><span data-feather="check-circle"></span></i><div><span>{{ $ar ? 'مؤكدة اليوم' : 'Confirmed today' }}</span><strong>{{ number_format($summary['confirmed']) }}</strong></div></article>
        <article class="venue-metric"><i><span data-feather="clock"></span></i><div><span>{{ $ar ? 'حجوزات قادمة' : 'Upcoming' }}</span><strong>{{ number_format($summary['upcoming']) }}</strong></div></article>
        <article class="venue-metric"><i><span data-feather="credit-card"></span></i><div><span>{{ $ar ? 'تحصيل اليوم' : 'Today collected' }}</span><strong>{{ number_format($summary['todayRevenue'], 2) }}</strong></div></article>
    </section>

    <section class="venue-calendar-shell">
        <aside class="venue-calendar-side">
            <h4>{{ $ar ? 'تصفية التقويم' : 'Filter calendar' }}</h4>
            <div class="venue-filter"><label for="spaceFilter">{{ $ar ? 'الملعب أو المساحة' : 'Venue or space' }}</label><select id="spaceFilter"><option value="">{{ $ar ? 'جميع المساحات' : 'All spaces' }}</option>@foreach($spaces as $space)<option value="{{ $space->id }}">{{ $space->venue->name }} - {{ $space->name }}</option>@endforeach</select></div>
            <div class="venue-filter"><label for="statusFilter">{{ $ar ? 'حالة الحجز' : 'Booking status' }}</label><select id="statusFilter"><option value="">{{ $ar ? 'جميع الحالات' : 'All statuses' }}</option>@foreach($statusLabels as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
            <div class="venue-legend">@foreach(['confirmed'=>'#2563eb','pending'=>'#d97706','checked_in'=>'#059669','completed'=>'#64748b','cancelled'=>'#dc2626','no_show'=>'#7c3aed'] as $status=>$color)<span><i style="background:{{ $color }}"></i>{{ $statusLabels[$status] }}</span>@endforeach</div>
            <div class="venue-today-list"><h4>{{ $ar ? 'جدول اليوم' : 'Today schedule' }}</h4>@forelse($todayBookings as $booking)<a class="venue-today-item" href="{{ route('academy.venue-bookings.edit',$booking) }}"><strong>{{ $booking->starts_at->format('H:i') }} · {{ $booking->space?->name }}</strong><small>{{ $booking->customer?->name }} · {{ $statusLabels[$booking->status] ?? $booking->status }}</small></a>@empty<p class="text-muted small">{{ $ar ? 'لا توجد حجوزات اليوم.' : 'No bookings today.' }}</p>@endforelse</div>
        </aside>
        <div class="venue-calendar-main">
            <div class="venue-calendar-toolbar">
                <div class="venue-calendar-nav"><button data-action="prev" aria-label="{{ $ar?'السابق':'Previous' }}"><i data-feather="chevron-right"></i></button><button data-action="today">{{ $ar?'اليوم':'Today' }}</button><button data-action="current">{{ $ar?'الوقت الحالي':'Current time' }}</button><button data-action="next" aria-label="{{ $ar?'التالي':'Next' }}"><i data-feather="chevron-left"></i></button></div>
                <h3 id="venueCalendarTitle"></h3>
                <div class="venue-calendar-views"><button data-view="dayGridMonth">{{ $ar?'شهر':'Month' }}</button><button data-view="timeGridWeek">{{ $ar?'أسبوع':'Week' }}</button><button data-view="timeGridDay">{{ $ar?'يوم':'Day' }}</button><button data-view="listWeek">{{ $ar?'قائمة':'List' }}</button></div>
            </div>
            <div id="venueBookingCalendar"></div>
        </div>
    </section>
</div>

<div id="bookingDrawerBackdrop" class="booking-drawer-backdrop"></div>
<aside id="bookingDrawer" class="booking-drawer" aria-hidden="true">
    <header><div><small id="drawerReference"></small><h3 id="drawerTitle"></h3></div><button id="drawerClose" class="drawer-close"><i data-feather="x"></i></button></header>
    <dl><div><dt>{{ $ar?'الموعد':'Schedule' }}</dt><dd id="drawerSchedule"></dd></div><div><dt>{{ $ar?'الموقع والمساحة':'Venue & space' }}</dt><dd id="drawerSpace"></dd></div><div><dt>{{ $ar?'العميل':'Customer' }}</dt><dd id="drawerCustomer"></dd></div><div><dt>{{ $ar?'الحالة':'Status' }}</dt><dd id="drawerStatus"></dd></div><div><dt>{{ $ar?'المدفوع / الإجمالي':'Paid / total' }}</dt><dd id="drawerPayment"></dd></div></dl>
    <a id="drawerEdit" class="venue-action primary w-100 justify-content-center" href="#"><i data-feather="edit-2"></i>{{ $ar?'تعديل الحجز':'Edit booking' }}</a>
</aside>
@endsection

@push('js')
<script src="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('assetsAdmin/src/plugins/src/fullcalendar/locales-all.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allEvents = @json($events);
    const labels = @json($statusLabels);
    const ar = @json($ar);
    const calendarElement = document.getElementById('venueBookingCalendar');
    const spaceFilter = document.getElementById('spaceFilter');
    const statusFilter = document.getElementById('statusFilter');
    const drawer = document.getElementById('bookingDrawer');
    const backdrop = document.getElementById('bookingDrawerBackdrop');
    const format = new Intl.DateTimeFormat(ar ? 'ar-EG' : 'en-GB', {dateStyle:'medium',timeStyle:'short'});
    const calendar = new FullCalendar.Calendar(calendarElement, {
        locale: ar ? 'ar' : 'en', direction: ar ? 'rtl' : 'ltr', initialView: innerWidth < 768 ? 'listWeek' : 'timeGridWeek',
        firstDay: 6, nowIndicator: true, allDaySlot: false, slotMinTime:'06:00:00', slotMaxTime:'24:00:00', slotDuration:'00:30:00',
        height:'auto', expandRows:true, selectable:true, headerToolbar:false, events: allEvents,
        eventTimeFormat:{hour:'2-digit',minute:'2-digit',meridiem:'short'}, slotLabelFormat:{hour:'2-digit',minute:'2-digit',meridiem:'short'},
        eventClick(info){ openDrawer(info.event); },
        select(info){ if(!info.allDay){ const qs=new URLSearchParams({date:info.startStr.slice(0,10),start_time:info.startStr.slice(11,16),end_time:info.endStr.slice(11,16),venue_space_id:spaceFilter.value}); location.href=@json(route('academy.venue-bookings.create'))+'?'+qs; } },
        datesSet(info){ document.getElementById('venueCalendarTitle').textContent=info.view.title; document.querySelectorAll('[data-view]').forEach(b=>b.classList.toggle('is-active',b.dataset.view===info.view.type)); }
    });
    calendar.render();
    function filter(){ calendar.removeAllEvents(); calendar.addEventSource(allEvents.filter(e=>(!spaceFilter.value||String(e.spaceId)===spaceFilter.value)&&(!statusFilter.value||e.status===statusFilter.value))); }
    spaceFilter.addEventListener('change',filter); statusFilter.addEventListener('change',filter);
    document.querySelectorAll('[data-view]').forEach(b=>b.addEventListener('click',()=>calendar.changeView(b.dataset.view)));
    document.querySelectorAll('[data-action]').forEach(b=>b.addEventListener('click',()=>{ if(b.dataset.action==='current'){calendar.changeView('timeGridDay',new Date());calendar.scrollToTime(new Date().toTimeString().slice(0,8));}else calendar[b.dataset.action](); }));
    function openDrawer(event){ const p=event.extendedProps; document.getElementById('drawerReference').textContent=p.reference||'';document.getElementById('drawerTitle').textContent=event.title;document.getElementById('drawerSchedule').textContent=format.format(event.start)+' - '+event.end.toLocaleTimeString(ar?'ar-EG':'en-GB',{hour:'2-digit',minute:'2-digit'});document.getElementById('drawerSpace').textContent=(p.venue||'')+' - '+(p.space||'');document.getElementById('drawerCustomer').textContent=(p.customer||'')+' · '+(p.phone||'');document.getElementById('drawerStatus').textContent=labels[p.status]||p.status;document.getElementById('drawerPayment').textContent=Number(p.paid).toFixed(2)+' / '+Number(p.total).toFixed(2);document.getElementById('drawerEdit').href=p.editUrl;drawer.classList.add('is-open');backdrop.classList.add('is-open');drawer.setAttribute('aria-hidden','false'); }
    function close(){drawer.classList.remove('is-open');backdrop.classList.remove('is-open');drawer.setAttribute('aria-hidden','true');}
    document.getElementById('drawerClose').addEventListener('click',close);backdrop.addEventListener('click',close);document.addEventListener('keydown',e=>{if(e.key==='Escape')close()});
    if(window.feather) feather.replace();
});
</script>
@endpush
