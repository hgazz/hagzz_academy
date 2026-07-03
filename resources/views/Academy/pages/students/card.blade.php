<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $student->name }} - {{ app()->getLocale()==='ar'?'بطاقة عضو':'Member card' }}</title>
<style>
@page{size:A4;margin:12mm}*{box-sizing:border-box}body{margin:0;background:#eef2f6;color:#102a43;font-family:Arial,"Segoe UI",sans-serif}.printbar{display:flex;justify-content:center;gap:8px;padding:15px}.printbar button,.printbar a{padding:10px 16px;border:0;border-radius:6px;background:#0f766e;color:#fff;text-decoration:none;font-weight:700}.member-card{position:relative;width:85.6mm;height:53.98mm;margin:20px auto;overflow:hidden;border:1px solid #0b3b3b;border-radius:4mm;background:#fff;box-shadow:0 12px 30px #1e293b22}.card-accent{position:absolute;inset-block:0;inset-inline-start:0;width:29mm;background:#07585a}.card-accent:after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(145deg,transparent 0 8mm,#ffffff12 8mm 8.5mm)}.portrait{position:absolute;z-index:2;inset-inline-start:5mm;top:10mm;width:24mm;height:30mm;object-fit:cover;border:1.2mm solid #fff;border-radius:3mm;background:#fff}.brand{position:absolute;top:4mm;inset-inline-end:5mm;display:flex;align-items:center;gap:2mm;max-width:48mm}.brand img{width:9mm;height:9mm;object-fit:contain}.brand strong{font-size:3.5mm;line-height:1.2}.card-type{position:absolute;top:15mm;inset-inline-end:5mm;color:#0f766e;font-size:2.4mm;font-weight:700}.student-name{position:absolute;top:20mm;inset-inline-end:5mm;width:48mm;margin:0;font-size:5mm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.member-code{position:absolute;top:27mm;inset-inline-end:5mm;width:48mm;font:700 3.2mm monospace;direction:ltr;text-align:end}.details{position:absolute;top:33mm;inset-inline-end:22mm;width:31mm;display:grid;gap:1.2mm;font-size:2.2mm}.details div{display:flex;justify-content:space-between;border-bottom:.2mm solid #d7e2e7;padding-bottom:.6mm}.details span{color:#64748b}.status{position:absolute;inset-inline-end:22mm;bottom:4mm;padding:1.3mm 2.5mm;border-radius:1.5mm;background:#dcfce7;color:#166534;font-size:2.2mm;font-weight:700}.qr{position:absolute;inset-inline-end:4mm;bottom:5mm;width:16mm;height:16mm}.barcode{position:absolute;z-index:3;inset-inline-start:3mm;bottom:2.5mm;width:31mm;height:7mm;background:#fff;padding:.6mm}.barcode svg{width:100%;height:100%}.powered{position:absolute;bottom:1.5mm;inset-inline-end:4mm;color:#64748b;font-size:1.55mm}.card-note{max-width:85.6mm;margin:0 auto;text-align:center;color:#64748b;font-size:12px}
@media print{body{background:#fff}.printbar,.card-note{display:none}.member-card{margin:0;box-shadow:none;break-inside:avoid}}
</style>
</head>
@php
 $ar=app()->getLocale()==='ar';
 $sport=$student->groups->first()?->sport?->name;
 $group=$subscription?->group?->name ?: $student->groups->first()?->name;
@endphp
<body>
<div class="printbar"><button onclick="window.print()">{{ $ar?'طباعة الكارت':'Print card' }}</button><a href="{{ route('academy.students.index') }}">{{ $ar?'العودة للطلاب':'Back to students' }}</a></div>
<article class="member-card">
 <div class="card-accent"></div>
 <img class="portrait" src="{{ $student->avatarUrl() }}" onerror="this.onerror=null;this.src='{{ $student->defaultImageUrl() }}'" alt="">
 <div class="brand"><img src="{{ $academy->logo }}" onerror="this.style.display='none'" alt=""><strong>{{ $academy->commercial_name }}</strong></div>
 <span class="card-type">{{ $ar?'بطاقة عضو ورياضي':'Member & athlete card' }}</span>
 <h1 class="student-name">{{ $student->name }}</h1>
 <div class="member-code">{{ $membershipCode }}</div>
 <div class="details"><div><span>{{ $ar?'الرياضة':'Sport' }}</span><b>{{ $sport ?: '-' }}</b></div><div><span>{{ $ar?'المجموعة':'Group' }}</span><b>{{ $group ?: '-' }}</b></div><div><span>{{ $ar?'صالح حتى':'Valid until' }}</span><b>{{ $subscription?->ends_on?->format('Y-m-d') ?: '-' }}</b></div></div>
 <span class="status">{{ $subscription?->status==='active'?($ar?'اشتراك نشط':'Active subscription'):($ar?'تحقق من الاشتراك':'Check subscription') }}</span>
 <img class="qr" src="{{ $qrDataUri }}" alt="QR">
 <div class="barcode">{!! $barcodeSvg !!}</div>
 <small class="powered">{{ $ar?'منظومة حجز الرقمية - شركة ميسك القطرية':'Hagzz Digital Platform - Misk Qatar' }}</small>
</article>
<p class="card-note">{{ $ar?'مقاس الكارت القياسي CR80 وجاهز للطباعة على PVC أو ورق A4.':'Standard CR80 size, ready for PVC or A4 printing.' }}</p>
</body></html>
