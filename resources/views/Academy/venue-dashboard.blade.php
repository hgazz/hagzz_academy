@extends('Academy.Layouts.master')
@section('title', trans('admin.dashboard'))

@push('css')
<link href="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.css') }}" rel="stylesheet">
<style>
    .vd{--ink:#102a43;--muted:#64748b;--line:#e2e8f0;--brand:#0f766e}.vd-head{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:22px 0}.vd-head h1{font-size:26px;color:var(--ink);margin:0 0 5px}.vd-head p{color:var(--muted);margin:0}.vd-actions{display:flex;gap:9px;flex-wrap:wrap}.vd-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:9px 14px;border:1px solid var(--line);border-radius:7px;background:#fff;color:var(--ink);font-weight:700}.vd-btn.primary{background:var(--brand);border-color:var(--brand);color:#fff}.vd-btn svg{width:17px}
    .vd-welcome{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:23px;margin-bottom:15px;border-radius:8px;background:linear-gradient(135deg,#0f766e,#155e75);color:#fff}.vd-welcome span,.vd-welcome p{color:rgba(255,255,255,.78)}.vd-welcome h2{color:#fff;margin:4px 0}.vd-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:15px}.vd-card,.vd-panel{background:#fff;border:1px solid var(--line);border-radius:8px}.vd-card{display:flex;align-items:center;gap:13px;padding:16px;min-height:100px}.vd-card i{display:grid;place-items:center;width:43px;height:43px;flex:0 0 43px;border-radius:8px;background:#ccfbf1;color:#0f766e}.vd-card:nth-child(2) i{background:#dbeafe;color:#1d4ed8}.vd-card:nth-child(3) i{background:#fef3c7;color:#b45309}.vd-card:nth-child(4) i{background:#ede9fe;color:#7c3aed}.vd-card svg{width:21px}.vd-card span,.vd-card small{display:block;color:var(--muted);font-size:12px}.vd-card strong{display:block;color:var(--ink);font-size:22px}
    .vd-grid{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(300px,.75fr);gap:15px;margin-bottom:15px}.vd-panel{padding:18px;min-width:0}.vd-panel header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}.vd-panel h3{color:var(--ink);font-size:17px;margin:0}.vd-panel header p{color:var(--muted);font-size:12px;margin:4px 0 0}.vd-link{color:var(--brand);font-weight:700;font-size:12px}.vd-spaces{display:grid;gap:9px}.vd-space{display:grid;grid-template-columns:1fr auto;gap:8px;padding:12px;border:1px solid var(--line);border-radius:7px}.vd-space strong{color:var(--ink)}.vd-space span,.vd-space small{color:var(--muted);font-size:12px}.vd-space b{color:var(--brand)}
    .vd-table{width:100%;border-collapse:collapse}.vd-table th{color:var(--muted);font-size:11px;text-align:start;padding:10px;border-bottom:1px solid var(--line)}.vd-table td{padding:11px 10px;border-bottom:1px solid #f1f5f9;color:#334155;font-size:13px}.vd-status{display:inline-flex;padding:5px 8px;border-radius:999px;background:#e0f2fe;color:#0369a1;font-size:11px;font-weight:700}.vd-empty{text-align:center;color:var(--muted);padding:30px}
    @media(max-width:1100px){.vd-metrics{grid-template-columns:repeat(2,1fr)}.vd-grid{grid-template-columns:1fr}}@media(max-width:650px){.vd-head,.vd-welcome{align-items:flex-start;flex-direction:column}.vd-actions{width:100%}.vd-btn{flex:1}.vd-metrics{grid-template-columns:1fr}.vd-panel{padding:13px}.vd-table{min-width:650px}.vd-table-wrap{overflow-x:auto}}
</style>
@endpush

@php
    $ar=app()->getLocale()==='ar';
    $status=['pending'=>$ar?'قيد الانتظار':'Pending','confirmed'=>$ar?'مؤكد':'Confirmed','checked_in'=>$ar?'حضر':'Checked in','completed'=>$ar?'مكتمل':'Completed','cancelled'=>$ar?'ملغي':'Cancelled','no_show'=>$ar?'لم يحضر':'No show'];
@endphp
@section('content')
<div class="middle-content container-xxl p-0 vd" dir="{{ $ar?'rtl':'ltr' }}">
    <header class="vd-head"><div><h1>{{ $ar?'لوحة إدارة الملاعب':'Venue operations dashboard' }}</h1><p>{{ now()->locale(app()->getLocale())->translatedFormat('d F Y') }}</p></div><div class="vd-actions"><a class="vd-btn" href="{{ route('academy.venue-bookings.calendar') }}"><i data-feather="calendar"></i>{{ $ar?'تقويم الحجوزات':'Booking calendar' }}</a><a class="vd-btn primary" href="{{ route('academy.venue-bookings.create') }}"><i data-feather="plus"></i>{{ $ar?'حجز جديد':'New booking' }}</a></div></header>
    <section class="vd-welcome"><div><span>{{ $ar?'مرحبًا بعودتك':'Welcome back' }}</span><h2>{{ $ownerName ?: $academyName }}</h2><p>{{ $academyName }} · {{ $ar?'ملخص تشغيل الملاعب والحجوزات والمدفوعات اليوم.':'Today’s venue, booking and payment overview.' }}</p></div><i data-feather="map"></i></section>
    <section class="vd-metrics">
        <article class="vd-card"><i><span data-feather="calendar"></span></i><div><span>{{ $ar?'حجوزات اليوم':'Today bookings' }}</span><strong>{{ number_format($venueDashboard['todayBookings']) }}</strong><small>{{ number_format($venueDashboard['upcoming']) }} {{ $ar?'قادمة':'upcoming' }}</small></div></article>
        <article class="vd-card"><i><span data-feather="credit-card"></span></i><div><span>{{ $ar?'تحصيل اليوم':'Today collected' }}</span><strong>{{ number_format($venueDashboard['todayCollected'],2) }}</strong><small>{{ $ar?'من الحجوزات':'from bookings' }}</small></div></article>
        <article class="vd-card"><i><span data-feather="home"></span></i><div><span>{{ $ar?'المواقع والمساحات':'Venues & spaces' }}</span><strong>{{ $venueDashboard['venues'] }} / {{ $venueDashboard['spaces'] }}</strong><small>{{ $ar?'موقع / مساحة نشطة':'active venues / spaces' }}</small></div></article>
        <article class="vd-card"><i><span data-feather="alert-circle"></span></i><div><span>{{ $ar?'مبالغ متبقية':'Outstanding' }}</span><strong>{{ number_format($venueDashboard['outstanding'],2) }}</strong><small>{{ number_format($venueDashboard['totalCollected'],2) }} {{ $ar?'محصل':'collected' }}</small></div></article>
    </section>
    <section class="vd-grid">
        <article class="vd-panel"><header><div><h3>{{ $ar?'الحجوزات والإيرادات':'Bookings & revenue' }}</h3><p>{{ $ar?'أداء آخر 12 شهرًا':'Last 12 months performance' }}</p></div><span class="vd-link">{{ ($venueDashboard['bookingTrend']>=0?'+':'').$venueDashboard['bookingTrend'] }}%</span></header><div id="venueFinancialChart"></div></article>
        <article class="vd-panel"><header><div><h3>{{ $ar?'حالات الحجوزات':'Booking statuses' }}</h3><p>{{ $ar?'التوزيع الإجمالي':'Overall distribution' }}</p></div></header><div id="venueStatusChart"></div></article>
    </section>
    <section class="vd-grid">
        <article class="vd-panel"><header><div><h3>{{ $ar?'أحدث الحجوزات':'Recent bookings' }}</h3><p>{{ $ar?'آخر العمليات المسجلة':'Latest recorded activity' }}</p></div><a class="vd-link" href="{{ route('academy.venue-bookings.index') }}">{{ $ar?'عرض الكل':'View all' }}</a></header><div class="vd-table-wrap"><table class="vd-table"><thead><tr><th>{{ $ar?'المرجع':'Reference' }}</th><th>{{ $ar?'العميل':'Customer' }}</th><th>{{ $ar?'المساحة':'Space' }}</th><th>{{ $ar?'الموعد':'Schedule' }}</th><th>{{ $ar?'الحالة':'Status' }}</th></tr></thead><tbody>@forelse($venueDashboard['recentBookings'] as $booking)<tr><td><a href="{{ route('academy.venue-bookings.edit',$booking) }}">{{ $booking->reference }}</a></td><td>{{ $booking->customer?->name }}</td><td>{{ $booking->space?->venue?->name }} · {{ $booking->space?->name }}</td><td>{{ $booking->starts_at?->format('d/m H:i') }}</td><td><span class="vd-status">{{ $status[$booking->status]??$booking->status }}</span></td></tr>@empty<tr><td colspan="5" class="vd-empty">{{ $ar?'لا توجد حجوزات بعد':'No bookings yet' }}</td></tr>@endforelse</tbody></table></div></article>
        <article class="vd-panel"><header><div><h3>{{ $ar?'الأكثر حجزًا':'Top booked spaces' }}</h3><p>{{ $ar?'حسب عدد الحجوزات':'Ranked by bookings' }}</p></div><a class="vd-link" href="{{ route('academy.venue-spaces.index') }}">{{ $ar?'إدارة المساحات':'Manage spaces' }}</a></header><div class="vd-spaces">@forelse($venueDashboard['topSpaces'] as $space)<div class="vd-space"><div><strong>{{ $space->name }}</strong><small>{{ $space->venue?->name }}</small></div><b>{{ $space->bookings_count }}</b></div>@empty<div class="vd-empty">{{ $ar?'لا توجد مساحات بعد':'No spaces yet' }}</div>@endforelse</div></article>
    </section>
</div>
@endsection

@push('js')
<script src="{{ asset('assetsAdmin/src/plugins/src/apex/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const finiteNumbers=values=>Array.from(values||[],value=>{const number=Number(value);return Number.isFinite(number)?number:0});
 const d={labels:@json($venueDashboard['monthLabels']),bookings:finiteNumbers(@json($venueDashboard['monthlyBookings'])),revenue:finiteNumbers(@json($venueDashboard['monthlyRevenue'])),collected:finiteNumbers(@json($venueDashboard['monthlyCollected'])),statuses:finiteNumbers(@json($venueDashboard['statuses']))};
 const common={fontFamily:'Cairo, Nunito, sans-serif',foreColor:'#64748b',toolbar:{show:false}};
 new ApexCharts(document.querySelector('#venueFinancialChart'),{chart:{...common,type:'line',height:350},series:[{name:@json($ar?'الحجوزات':'Bookings'),type:'column',data:d.bookings},{name:@json($ar?'قيمة الحجوزات':'Booking value'),type:'area',data:d.revenue},{name:@json($ar?'المحصل':'Collected'),type:'line',data:d.collected}],colors:['#2563eb','#14b8a6','#7c3aed'],stroke:{width:[0,3,3],curve:'smooth'},dataLabels:{enabled:false},xaxis:{categories:d.labels},plotOptions:{bar:{borderRadius:4,columnWidth:'42%'}},legend:{position:'top'}}).render();
 const statusElement=document.querySelector('#venueStatusChart');
 if(d.statuses.reduce((sum,value)=>sum+value,0)>0){new ApexCharts(statusElement,{chart:{...common,type:'donut',height:350},series:d.statuses,labels:@json(array_values($status)),colors:['#d97706','#2563eb','#059669','#64748b','#dc2626','#7c3aed'],stroke:{width:0},legend:{position:'bottom'},plotOptions:{pie:{donut:{size:'70%'}}}}).render()}else{statusElement.classList.add('vd-empty');statusElement.style.minHeight='350px';statusElement.style.display='grid';statusElement.style.placeItems='center';statusElement.textContent=@json($ar?'لا توجد بيانات بعد':'No data available')}
 if(window.feather)feather.replace();
});
</script>
@endpush
