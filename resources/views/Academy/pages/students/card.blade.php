@php
    $ar = app()->getLocale() === 'ar';
    $isBulk = (bool) ($isBulk ?? false);
    
    // إعدادات العرض المتقدمة
    $orientation = in_array($orientation ?? request()->input('orientation', 'landscape'), ['landscape', 'portrait'], true) ? ($orientation ?? request()->input('orientation', 'landscape')) : 'landscape';
    $cardTheme   = in_array($theme ?? request()->input('theme', 'light'), ['light', 'dark', 'gold'], true) ? ($theme ?? request()->input('theme', 'light')) : 'light';
    $sideOption  = in_array($side ?? request()->input('side', 'front'), ['front', 'both', 'back'], true) ? ($side ?? request()->input('side', 'front')) : 'front';
    $rawLayout   = $layout ?? request()->input('layout', 4);
    $layout      = in_array((int)$rawLayout, [1, 2, 4, 6, 8], true) ? (int)$rawLayout : ($orientation === 'portrait' ? 6 : 4);
    
    $facilityType = $academy?->business_type ?? 'academy';
    $cardTitle    = facility_term('member_card_type', $facilityType);
    $backLabel    = facility_term('back_to_students', $facilityType);

    // تجميع المشتركين المستهدفين
    $rawStudents = $isBulk ? ($students ?? collect()) : collect();
    if (!$isBulk && isset($student) && $student) {
        $copiesCount = in_array($layout, [1, 2, 4, 6, 8], true) ? $layout : 1;
        for ($c = 0; $c < $copiesCount; $c++) {
            $rawStudents->push($student);
        }
    }

    // بناء وحدات الكروت مع دعم الوجهين (Front / Back / Both)
    $targetCards = collect();
    foreach ($rawStudents as $st) {
        if ($sideOption === 'front') {
            $targetCards->push(['member' => $st, 'side' => 'front']);
        } elseif ($sideOption === 'back') {
            $targetCards->push(['member' => $st, 'side' => 'back']);
        } else { // both
            $targetCards->push(['member' => $st, 'side' => 'front']);
            $targetCards->push(['member' => $st, 'side' => 'back']);
        }
    }

    $chunkSize = in_array($layout, [1, 2, 4, 6, 8], true) ? $layout : ($orientation === 'portrait' ? 6 : 4);
    $pages = $targetCards->chunk($chunkSize);

    // شروط وتعليمات الظهر المخصصة حسب نوع المنشأة
    $facilityRules = match($facilityType) {
        'gym' => [
            $ar ? 'هذه البطاقة شخصية وتعتبر وثيقة الدخول الرسمية للصالة.' : 'Personal card and official gym entry pass.',
            $ar ? 'يرجى مسح رمز QR عند أجهزة الدخول الذاتي لتسجيل الحضور.' : 'Scan QR code at automated turnstiles for check-in.',
            $ar ? 'الالتزام بقواعد السلامة والزي الرياضي المناسب وإرشادات الكباتن.' : 'Follow gym dress code and safety regulations.',
            $ar ? 'في حال فقدان البطاقة يرجى مراجعة إدارة الاستقبال فوراً.' : 'Report lost cards immediately to reception.',
        ],
        'health_center' => [
            $ar ? 'هذه البطاقة خاصة بمشترك المركز الصحي والسبا، للاستفادة من الباقات المحجوزة.' : 'Designated for Health Center & Spa members to access registered sessions.',
            $ar ? 'يجب إبراز البطاقة للأخصائي قبل بدء أي جلسة أو استخدام المرافق المائية.' : 'Present card to therapist prior to treatment or aquatic facility access.',
            $ar ? 'يرجى إبلاغ الفريق الطبي بأي حالات حساسية أو إصابات مسجلة بالبطاقة.' : 'Inform wellness staff of any recorded medical conditions or care alerts.',
            $ar ? 'في حال فقدان البطاقة يرجى إبلاغ الاستقبال فوراً لضمان حفظ الرصيد.' : 'Report lost cards immediately to front desk to protect your balance.',
        ],
        default => [
            $ar ? 'هذه البطاقة معتمدة رسمياً لمشتركي الأكاديمية لحضور الحصص والتمارين.' : 'Official membership card for academy attendance and training sessions.',
            $ar ? 'يجب إبراز البطاقة أو مسح الرمز عند دخول الملاعب والمرافق.' : 'Must be scanned or shown upon entering sports courts and facilities.',
            $ar ? 'الالتزام بالروح الرياضية والزي الرسمي المعتمد للأكاديمية.' : 'Uphold sportsmanship and wear official academy uniform at all times.',
            $ar ? 'في حال الفقدان أو التلف يرجى مراجعة إدارة الأكاديمية.' : 'Contact academy administration in case of damage or loss.',
        ]
    };
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $isBulk ? ($ar ? 'طباعة كروت الأعضاء والمشتركين' : 'Print Members Cards') : (($student?->name ?? ($ar ? 'كارت العضو' : 'Member Card')) . ' — ' . $cardTitle) }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Inter:wght@400;600;700;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ═══════════════════════════════════════════════════════════════════════
   نظام طباعة بطاقات العضوية الفاخرة — Hagzz Executive Cards Suite
   يدعم: (Landscape / Portrait) + (Front / Back / Dual-Sided) + 3 Themes
   ═══════════════════════════════════════════════════════════════════════ */
@page {
    size: A4 portrait;
    margin: 8mm 6mm;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    background: #090d16;
    color: #1e293b;
    font-family: 'Tajawal', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

/* ── شريط التحكم العلوي المتطور ────────────────────────────────────────── */
.controls-bar {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #111827;
    border-bottom: 1px solid #1f2937;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    flex-wrap: wrap;
    gap: 12px;
    color: #f8fafc;
}
.controls-left, .controls-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-control {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}
.btn-primary-action {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
}
.btn-primary-action:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-1px);
}
.btn-outline-back {
    background: #1f2937;
    color: #f1f5f9;
    border-color: #374151;
}
.btn-outline-back:hover {
    background: #374151;
}
.segmented-wrap {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.segmented-label {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 700;
}
.segmented-group {
    display: inline-flex;
    align-items: center;
    background: #0b0f19;
    border: 1px solid #2d3748;
    border-radius: 8px;
    padding: 2.5px;
    gap: 2.5px;
}
.btn-segmented {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.btn-segmented:hover {
    color: #f8fafc;
}
.btn-segmented.active {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
}
.btn-segmented.theme-dark-btn.active {
    background: #000000;
    color: #fbbf24;
    border: 1px solid #d97706;
}
.btn-segmented.theme-gold-btn.active {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #ffffff;
}
.meta-info-tag {
    font-size: 12.5px;
    color: #94a3b8;
    font-weight: 600;
}

/* ── صفحات الطباعة A4 ─────────────────────────────────────────────── */
.print-pages-container {
    max-width: 210mm;
    margin: 20px auto;
    padding: 0 10px;
}
.a4-sheet {
    background: #ffffff;
    box-shadow: 0 16px 45px rgba(0, 0, 0, 0.4);
    border-radius: 6px;
    padding: 10mm 7mm;
    margin-bottom: 25px;
    break-after: page;
    page-break-after: always;
    display: grid;
    justify-content: center;
    align-content: start;
}
.a4-sheet:last-child {
    break-after: auto;
    page-break-after: auto;
}

/* توزيع الكروت في الشبكة حسب الاتجاه والتقسيم */
.a4-sheet.orient-landscape.layout-1 {
    grid-template-columns: 86mm;
    justify-content: center;
    padding: 50mm 0;
}
.a4-sheet.orient-landscape.layout-2 {
    grid-template-columns: 86mm;
    gap: 20mm;
    justify-content: center;
    padding: 30mm 0;
}
.a4-sheet.orient-landscape.layout-4 {
    grid-template-columns: repeat(2, 86mm);
    gap: 14mm 10mm;
}
.a4-sheet.orient-landscape.layout-8 {
    grid-template-columns: repeat(2, 86mm);
    gap: 6mm 8mm;
}

.a4-sheet.orient-portrait.layout-1 {
    grid-template-columns: 54mm;
    justify-content: center;
    padding: 40mm 0;
}
.a4-sheet.orient-portrait.layout-2 {
    grid-template-columns: repeat(2, 54mm);
    gap: 20mm 15mm;
    justify-content: center;
    padding: 35mm 0;
}
.a4-sheet.orient-portrait.layout-4 {
    grid-template-columns: repeat(2, 54mm);
    gap: 16mm 14mm;
    justify-content: center;
}
.a4-sheet.orient-portrait.layout-6 {
    grid-template-columns: repeat(3, 54mm);
    gap: 12mm 10mm;
}
.a4-sheet.orient-portrait.layout-8 {
    grid-template-columns: repeat(4, 46mm);
    gap: 6mm 6mm;
}

/* ══════════════════════════════════════════════════════════════════════
   إطار الكارت وعلامات القص
   ══════════════════════════════════════════════════════════════════════ */
.card-outer-wrap {
    position: relative;
    display: inline-block;
}
.card-outer-wrap.card-is-landscape {
    width: 86mm;
    height: 54mm;
}
.card-outer-wrap.card-is-portrait {
    width: 54mm;
    height: 86mm;
}

.cut-mark {
    position: absolute;
    width: 3mm;
    height: 3mm;
    pointer-events: none;
    z-index: 10;
}
.cut-mark-tl { top: -1.8mm; left: -1.8mm; border-top: 0.3mm dashed #94a3b8; border-left: 0.3mm dashed #94a3b8; }
.cut-mark-tr { top: -1.8mm; right: -1.8mm; border-top: 0.3mm dashed #94a3b8; border-right: 0.3mm dashed #94a3b8; }
.cut-mark-bl { bottom: -1.8mm; left: -1.8mm; border-bottom: 0.3mm dashed #94a3b8; border-left: 0.3mm dashed #94a3b8; }
.cut-mark-br { bottom: -1.8mm; right: -1.8mm; border-bottom: 0.3mm dashed #94a3b8; border-right: 0.3mm dashed #94a3b8; }

.exec-card {
    position: relative;
    border-radius: 3.2mm;
    overflow: hidden;
    break-inside: avoid;
    page-break-inside: avoid;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.exec-card.card-is-landscape {
    width: 86mm;
    height: 54mm;
}
.exec-card.card-is-portrait {
    width: 54mm;
    height: 86mm;
}

/* ══════════════════════════════════════════════════════════════════════
   السمات والألوان (Themes)
   ══════════════════════════════════════════════════════════════════════ */
/* 1. النمط الفاتح (Light) */
.exec-card.theme-light {
    background: #ffffff;
    border: 0.7mm solid #cbd5e1;
    color: #0f172a;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
}
.exec-card.theme-light .card-top-stripe {
    height: 2mm;
    background: linear-gradient(90deg, #1e3a8a, #2563eb, #38bdf8);
}
.exec-card.theme-light.facility-health_center .card-top-stripe {
    background: linear-gradient(90deg, #831843, #db2777, #f472b6);
}
.exec-card.theme-light.facility-academy .card-top-stripe {
    background: linear-gradient(90deg, #065f46, #059669, #34d399);
}
.exec-card.theme-light .head-box {
    background: #f8fafc;
    border-bottom: 0.3mm solid #e2e8f0;
}
.exec-card.theme-light .foot-box {
    background: #f8fafc;
    border-top: 0.3mm solid #e2e8f0;
}
.exec-card.theme-light .brand-title { color: #0f172a; }
.exec-card.theme-light .brand-subtitle { color: #64748b; }
.exec-card.theme-light .member-full-name { color: #0f172a; }
.exec-card.theme-light .code-pill {
    color: #1e3a8a;
    background: #eff6ff;
    border: 0.25mm solid #bfdbfe;
}
.exec-card.theme-light .vip-tier-badge {
    background: #0f172a;
    color: #ffffff;
}
.exec-card.theme-light .qr-holder {
    background: #ffffff;
    border: 0.3mm solid #cbd5e1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.exec-card.theme-light .card-rule-item { color: #475569; }

/* 2. النمط الداكن الفاخر (Dark VIP) */
.exec-card.theme-dark {
    background: linear-gradient(145deg, #090d16 0%, #111827 50%, #080c14 100%);
    border: 0.7mm solid #374151;
    color: #f8fafc;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}
.exec-card.theme-dark .card-top-stripe {
    height: 2mm;
    background: linear-gradient(90deg, #d97706, #fbbf24, #d97706);
}
.exec-card.theme-dark .head-box {
    background: rgba(255, 255, 255, 0.04);
    border-bottom: 0.3mm solid rgba(255, 255, 255, 0.08);
}
.exec-card.theme-dark .foot-box {
    background: #060910;
    border-top: 0.3mm solid rgba(255, 255, 255, 0.08);
}
.exec-card.theme-dark .brand-title { color: #ffffff; }
.exec-card.theme-dark .brand-subtitle { color: #94a3b8; }
.exec-card.theme-dark .member-full-name { color: #ffffff; }
.exec-card.theme-dark .code-pill {
    color: #38bdf8;
    background: rgba(15, 23, 42, 0.8);
    border: 0.25mm solid #1e3a8a;
}
.exec-card.theme-dark .vip-tier-badge {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #ffffff;
    border: 0.2mm solid #fbbf24;
}
.exec-card.theme-dark .qr-holder {
    background: #ffffff;
    border: 0.3mm solid #475569;
}
.exec-card.theme-dark .card-rule-item { color: #cbd5e1; }
.exec-card.theme-dark .barcode-box {
    background: #ffffff;
    padding: 0.4mm 1mm;
    border-radius: 0.8mm;
}

/* 3. النمط الذهبي الملكي (Elite Gold) */
.exec-card.theme-gold {
    background: linear-gradient(145deg, #1c1917 0%, #292524 100%);
    border: 0.7mm solid #ca8a04;
    color: #fef08a;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.45);
}
.exec-card.theme-gold .card-top-stripe {
    height: 2mm;
    background: linear-gradient(90deg, #ca8a04, #fef08a, #ca8a04);
}
.exec-card.theme-gold .head-box {
    background: rgba(234, 179, 8, 0.06);
    border-bottom: 0.3mm solid rgba(234, 179, 8, 0.2);
}
.exec-card.theme-gold .foot-box {
    background: #141210;
    border-top: 0.3mm solid rgba(234, 179, 8, 0.2);
}
.exec-card.theme-gold .brand-title { color: #fef08a; }
.exec-card.theme-gold .brand-subtitle { color: #d6d3d1; }
.exec-card.theme-gold .member-full-name { color: #ffffff; }
.exec-card.theme-gold .code-pill {
    color: #facc15;
    background: rgba(0, 0, 0, 0.6);
    border: 0.25mm solid #ca8a04;
}
.exec-card.theme-gold .vip-tier-badge {
    background: #eab308;
    color: #000000;
}
.exec-card.theme-gold .qr-holder {
    background: #ffffff;
    border: 0.3mm solid #ca8a04;
}
.exec-card.theme-gold .card-rule-item { color: #e7e5e4; }
.exec-card.theme-gold .barcode-box {
    background: #ffffff;
    padding: 0.4mm 1mm;
    border-radius: 0.8mm;
}

/* ══════════════════════════════════════════════════════════════════════
   العناصر المشتركة (Shared Components)
   ══════════════════════════════════════════════════════════════════════ */
.status-pill {
    font-size: 1.7mm;
    font-weight: 900;
    padding: 0.4mm 1.6mm;
    border-radius: 0.8mm;
    white-space: nowrap;
    line-height: 1.1;
    display: inline-flex;
    align-items: center;
    gap: 0.8mm;
}
.status-pill.status-active { background: #dcfce7; color: #166534; border: 0.2mm solid #bbf7d0; }
.status-pill.status-frozen { background: #e0f2fe; color: #0369a1; border: 0.2mm solid #bae6fd; }
.status-pill.status-expired { background: #fee2e2; color: #991b1b; border: 0.2mm solid #fecaca; }

.care-alert-pill {
    background: #fef3c7;
    color: #92400e;
    border: 0.25mm solid #f59e0b;
    font-size: 1.65mm;
    font-weight: 900;
    padding: 0.4mm 1.4mm;
    border-radius: 0.8mm;
    white-space: nowrap;
    line-height: 1.1;
    display: inline-flex;
    align-items: center;
    gap: 0.8mm;
}

.vip-tier-badge {
    font-size: 1.8mm;
    font-weight: 900;
    padding: 0.7mm 2mm;
    border-radius: 0.9mm;
    white-space: nowrap;
    letter-spacing: 0.03em;
    display: inline-flex;
    align-items: center;
    gap: 0.8mm;
}

/* ══════════════════════════════════════════════════════════════════════
   1. التخطيط الأفقي — الوجه الأمامي (Landscape Front - VIP Pass)
   ══════════════════════════════════════════════════════════════════════ */
.landscape-front .head-box {
    height: 9mm;
    padding: 1.2mm 2.8mm;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.landscape-front .brand-group {
    display: flex;
    align-items: center;
    gap: 1.8mm;
    max-width: 58mm;
}
.landscape-front .brand-logo-img {
    width: 6.5mm;
    height: 6.5mm;
    object-fit: contain;
    border-radius: 1mm;
    background: #ffffff;
    padding: 0.3mm;
}
.landscape-front .brand-logo-alt {
    width: 6.5mm;
    height: 6.5mm;
    border-radius: 1mm;
    background: #1e3a8a;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.2mm;
}
.landscape-front .brand-titles {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.landscape-front .brand-title {
    font-size: 2.7mm;
    font-weight: 900;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 44mm;
}
.landscape-front .brand-subtitle {
    font-size: 1.55mm;
    font-weight: 700;
    letter-spacing: 0.02em;
    line-height: 1;
}

/* جسم الكارت الأفقي: 3 أعمدة فخمة متناسقة وبدون تكديس */
.landscape-front .body-box {
    height: 33mm;
    padding: 1.5mm 2.8mm;
    display: grid;
    grid-template-columns: 20mm 1fr 18mm;
    gap: 2.5mm;
    align-items: center;
}
.landscape-front .photo-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1mm;
}
.landscape-front .avatar-frame {
    position: relative;
    width: 19.5mm;
    height: 22.5mm;
    border-radius: 2mm;
    overflow: hidden;
    background: #e2e8f0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    border: 0.3mm solid rgba(0, 0, 0, 0.1);
}
.landscape-front .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.landscape-front .avatar-dot {
    position: absolute;
    bottom: 1mm;
    inset-inline-end: 1mm;
    width: 3.5mm;
    height: 3.5mm;
    border-radius: 50%;
    border: 0.4mm solid #ffffff;
    background: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
}

.landscape-front .details-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}
.landscape-front .member-full-name {
    margin: 0 0 1.2mm 0;
    font-size: 2.7mm;
    font-weight: 800;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: 0.01em;
}

.landscape-front .quick-meta-grid {
    display: flex;
    flex-direction: column;
    gap: 1.2mm;
}
.landscape-front .meta-item {
    display: flex;
    align-items: center;
    gap: 1.2mm;
    font-size: 1.95mm;
    line-height: 1.15;
}
.landscape-front .meta-lbl {
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.8mm;
}
.landscape-front .meta-val {
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 29mm;
}

.landscape-front .qr-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1mm;
}
.landscape-front .qr-holder {
    width: 17mm;
    height: 17mm;
    padding: 0.8mm;
    border-radius: 1.8mm;
    display: flex;
    align-items: center;
    justify-content: center;
}
.landscape-front .qr-holder img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}
.landscape-front .qr-sub {
    font-size: 1.6mm;
    font-weight: 800;
    color: #64748b;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.7mm;
}

.landscape-front .foot-box {
    height: 10mm;
    padding: 1mm 2.8mm;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.landscape-front .barcode-box {
    width: 48mm;
    height: 6.5mm;
    display: flex;
    align-items: center;
}
.landscape-front .barcode-box svg {
    width: 100%;
    height: 100%;
    display: block;
}
.landscape-front .security-tag {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    line-height: 1.1;
}
.landscape-front .sec-title {
    font-size: 1.8mm;
    font-weight: 900;
    letter-spacing: 0.04em;
    display: inline-flex;
    align-items: center;
    gap: 0.8mm;
}
.landscape-front .sec-sub {
    font-size: 1.4mm;
    color: #94a3b8;
    font-weight: 600;
}

/* ══════════════════════════════════════════════════════════════════════
   2. التخطيط الأفقي — ظهر الكارت (Landscape Back - Rules & Info)
   ══════════════════════════════════════════════════════════════════════ */
.landscape-back .head-box {
    height: 8mm;
    padding: 1.2mm 2.8mm;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.landscape-back .body-box {
    height: 35mm;
    padding: 1.5mm 3mm;
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 2.5mm;
}
.landscape-back .details-summary {
    display: flex;
    flex-direction: column;
    gap: 0.8mm;
    font-size: 1.8mm;
    border-inline-end: 0.25mm solid #cbd5e1;
    padding-inline-end: 2.5mm;
    justify-content: center;
}
.landscape-back .sum-row {
    display: flex;
    align-items: baseline;
    gap: 1.5mm;
    line-height: 1.15;
}
.landscape-back .sum-lbl {
    color: #64748b;
    font-weight: 700;
    font-size: 1.7mm;
    white-space: nowrap;
    min-width: 14mm;
}
.landscape-back .sum-val {
    font-weight: 800;
    color: #0f172a;
    font-size: 1.8mm;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 25mm;
}
.landscape-back .rules-list {
    display: flex;
    flex-direction: column;
    gap: 1.2mm;
    margin: 0;
    padding: 0;
    list-style: none;
    justify-content: center;
}
.landscape-back .card-rule-item {
    font-size: 1.6mm;
    line-height: 1.25;
    font-weight: 600;
    color: #475569;
    display: flex;
    align-items: flex-start;
    gap: 1mm;
}
.landscape-back .card-rule-item i {
    font-size: 1.6mm;
    margin-top: 0.3mm;
    color: #0284c7;
    flex-shrink: 0;
}
.landscape-back .foot-box {
    height: 9mm;
    padding: 1mm 2.8mm;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.landscape-back .barcode-full {
    width: 44mm;
    height: 6mm;
}
.landscape-back .barcode-full svg { width: 100%; height: 100%; }

/* ══════════════════════════════════════════════════════════════════════
   3. التخطيط الرأسي / باج تعليق (Portrait ID Badge)
   ══════════════════════════════════════════════════════════════════════ */
.card-is-portrait .lanyard-slot-mark {
    width: 11mm;
    height: 2.2mm;
    border: 0.3mm dashed #94a3b8;
    border-radius: 1mm;
    margin: 1.2mm auto 0 auto;
}
.portrait-front .head-box {
    padding: 1mm 2mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.8mm;
}
.portrait-front .body-box {
    padding: 1mm 2mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.8mm;
}
.portrait-front .avatar-frame {
    position: relative;
    width: 19mm;
    height: 22mm;
    border-radius: 2mm;
    overflow: hidden;
    background: #e2e8f0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
    border: 0.3mm solid rgba(0, 0, 0, 0.1);
}
.portrait-front .avatar-img { width: 100%; height: 100%; object-fit: cover; }
.portrait-front .avatar-dot {
    position: absolute;
    bottom: 0.8mm;
    inset-inline-end: 0.8mm;
    width: 3.2mm;
    height: 3.2mm;
    border-radius: 50%;
    border: 0.35mm solid #ffffff;
    background: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
}

.portrait-dates-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.2mm;
    background: #f1f5f9;
    border: 0.25mm solid #cbd5e1;
    border-radius: 1mm;
    padding: 0.4mm 1.5mm;
    margin: 0.3mm 0;
}
.theme-dark .portrait-dates-box {
    background: rgba(255, 255, 255, 0.06);
    border-color: #374151;
}
.theme-gold .portrait-dates-box {
    background: rgba(234, 179, 8, 0.1);
    border-color: #ca8a04;
}
.p-date-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    line-height: 1.1;
}
.p-date-lbl {
    font-size: 1.35mm;
    color: #64748b;
    font-weight: 700;
}
.theme-dark .p-date-lbl { color: #94a3b8; }
.theme-gold .p-date-lbl { color: #d6d3d1; }
.p-date-val {
    font-size: 1.75mm;
    font-weight: 800;
}
.p-date-sep {
    font-size: 1.8mm;
    color: #94a3b8;
    opacity: 0.7;
}

.portrait-front .member-full-name {
    margin: 0.6mm 0 0.8mm 0;
    font-size: 2.7mm;
    font-weight: 800;
    line-height: 1.2;
    max-width: 48mm;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: 0.01em;
}
.portrait-front .qr-holder {
    width: 14mm;
    height: 14mm;
    padding: 0.6mm;
    border-radius: 1.5mm;
}
.portrait-front .qr-holder img { width: 100%; height: 100%; object-fit: contain; }

.portrait-front .foot-box {
    padding: 0.8mm 2mm 2.2mm 2mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5mm;
}
.portrait-front .barcode-box {
    width: 30mm;
    height: 3.8mm;
    margin: 0 auto;
}
.portrait-front .barcode-box svg { width: 100%; height: 100%; display: block; }

/* ظهر الباج الرأسي */
.portrait-back .head-box {
    padding: 1mm 2mm;
    text-align: center;
}
.portrait-back .body-box {
    padding: 2mm;
    display: flex;
    flex-direction: column;
    gap: 1.2mm;
    font-size: 1.85mm;
}
.portrait-back .card-rule-item {
    font-size: 1.65mm;
    line-height: 1.2;
    margin-bottom: 1mm;
    display: flex;
    gap: 0.8mm;
}
.portrait-back .foot-box {
    padding: 1mm 2mm 2.2mm 2mm;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.portrait-back .barcode-box {
    width: 32mm;
    height: 4mm;
    margin: 0 auto;
}
.portrait-back .barcode-box svg { width: 100%; height: 100%; display: block; }

/* ══════════════════════════════════════════════════════════════════════
   الطباعة (@media print)
   ══════════════════════════════════════════════════════════════════════ */
@media print {
    body {
        background: #ffffff !important;
    }
    .controls-bar {
        display: none !important;
    }
    .print-pages-container {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .a4-sheet {
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 6mm 4mm !important;
    }
    .exec-card {
        box-shadow: none !important;
    }
}
</style>
</head>
<body>

{{-- ── شريط التحكم العلوي المتطور ─────────────────────────────────── --}}
<header class="controls-bar">
    <div class="controls-left">
        <button class="btn-control btn-primary-action" onclick="window.print()">
            <i class="fa-solid fa-print"></i>
            <span>{{ $ar ? 'طباعة الكروت الفورية (Ctrl + P)' : 'Print Cards (Ctrl + P)' }}</span>
        </button>

        <a class="btn-control btn-outline-back" href="{{ route('academy.students.index') }}">
            <i class="fa-solid fa-arrow-right"></i>
            <span>{{ $backLabel }}</span>
        </a>

        {{-- اتجاه الكارت: أفقي (محفظة) / رأسي (باج تعليق) --}}
        <div class="segmented-wrap">
            <span class="segmented-label">{{ $ar ? 'الاتجاه:' : 'Orient:' }}</span>
            <div class="segmented-group">
                <a href="{{ request()->fullUrlWithQuery(['orientation' => 'landscape', 'layout' => ($layout === 6 ? 4 : $layout)]) }}"
                   class="btn-segmented {{ $orientation === 'landscape' ? 'active' : '' }}">
                   <i class="fa-solid fa-id-card"></i> {{ $ar ? 'أفقي (محفظة)' : 'Landscape' }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['orientation' => 'portrait', 'layout' => ($layout === 4 ? 6 : $layout)]) }}"
                   class="btn-segmented {{ $orientation === 'portrait' ? 'active' : '' }}">
                   <i class="fa-solid fa-address-card"></i> {{ $ar ? 'رأسي (باج تعليق)' : 'Portrait Badge' }}
                </a>
            </div>
        </div>

        {{-- أوجه الطباعة: أمامي VIP / الوجهين معاً / الظهر فقط --}}
        <div class="segmented-wrap">
            <span class="segmented-label">{{ $ar ? 'الوجه:' : 'Side:' }}</span>
            <div class="segmented-group">
                <a href="{{ request()->fullUrlWithQuery(['side' => 'front']) }}"
                   class="btn-segmented {{ $sideOption === 'front' ? 'active' : '' }}">
                   {{ $ar ? 'الوجه الأمامي VIP' : 'Front Only' }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['side' => 'both']) }}"
                   class="btn-segmented {{ $sideOption === 'both' ? 'active' : '' }}" title="{{ $ar ? 'طباعة الوجه والظهر معاً للطي أو الوجهين' : 'Print Front and Back' }}">
                   {{ $ar ? 'الوجهين معاً' : 'Front & Back' }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['side' => 'back']) }}"
                   class="btn-segmented {{ $sideOption === 'back' ? 'active' : '' }}">
                   {{ $ar ? 'الظهر فقط' : 'Back Only' }}
                </a>
            </div>
        </div>

        {{-- الستايل اللوني الفاخر --}}
        <div class="segmented-wrap">
            <span class="segmented-label">{{ $ar ? 'الستايل:' : 'Theme:' }}</span>
            <div class="segmented-group">
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'light']) }}"
                   class="btn-segmented {{ $cardTheme === 'light' ? 'active' : '' }}">
                   <i class="fa-solid fa-sun"></i> {{ $ar ? 'فاتح عصري' : 'Light' }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'dark']) }}"
                   class="btn-segmented theme-dark-btn {{ $cardTheme === 'dark' ? 'active' : '' }}">
                   <i class="fa-solid fa-moon"></i> {{ $ar ? 'أسود VIP' : 'Black VIP' }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'gold']) }}"
                   class="btn-segmented theme-gold-btn {{ $cardTheme === 'gold' ? 'active' : '' }}">
                   <i class="fa-solid fa-crown"></i> {{ $ar ? 'ذهبي ملكي' : 'Gold' }}
                </a>
            </div>
        </div>

        {{-- تقسيم الورقة A4 --}}
        <div class="segmented-wrap">
            <span class="segmented-label">{{ $ar ? 'بالورقة:' : 'Per A4:' }}</span>
            <div class="segmented-group">
                @php $layoutOptions = $orientation === 'portrait' ? [1, 2, 4, 6] : [1, 2, 4, 8]; @endphp
                @foreach($layoutOptions as $opt)
                    <a href="{{ request()->fullUrlWithQuery(['layout' => $opt]) }}"
                       class="btn-segmented {{ $layout === $opt ? 'active' : '' }}">
                       {{ $opt }}
                       @if(($orientation === 'landscape' && $opt === 4) || ($orientation === 'portrait' && $opt === 6))
                           <small style="opacity:0.85">({{ $ar ? 'المثالي' : 'Best' }})</small>
                       @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="controls-right">
        @if(!$isBulk && isset($student))
            @php
                $cardPhone = preg_replace('/\D+/', '', (string) ($student->phone ?: $student->guardian_phone));
                if ($cardPhone && str_starts_with($cardPhone, '0')) $cardPhone = '2' . $cardPhone;
                $cardSub = $student->subscriptions->sortByDesc('starts_on')->first();
                $isMbrFrozen = $cardSub?->status === 'frozen';
                $isMbrSuspended = $student->status === 'suspended';
                $cardText = "مرحباً بك المشترك العزيز " . $student->name . " 🪪\n"
                    . "يسعدنا تزويدكم ببيانات بطاقة عضويتكم الرسمية لدى (" . ($academy?->name ?: 'المنشأة') . "):\n"
                    . "🆔 كود العضوية: " . ($student->computed_membership_code ?? \App\Support\MembershipCode::make($student)) . "\n"
                    . "📅 بداية الاشتراك: " . optional($cardSub?->starts_on)->format('Y-m-d') . "\n"
                    . "⏳ نهاية الاشتراك: " . optional($cardSub?->ends_on)->format('Y-m-d') . "\n"
                    . "📌 الباقة: " . ($cardSub?->group?->name ?: 'عضوية عامة') . "\n\n"
                    . "يمكنكم إبراز رمز QR عند البوابة الإلكترونية لتسجيل الحضور السريع! 🌟";
                $cardWaLink = $cardPhone ? ('https://api.whatsapp.com/send?phone=' . $cardPhone . '&text=' . urlencode($cardText)) : null;
            @endphp

            @if($isMbrSuspended || $isMbrFrozen)
                <span class="meta-info-tag" style="background:#fee2e2;border:1px solid #f87171;color:#dc2626;font-weight:800;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $isMbrSuspended ? ($ar ? 'حساب العضو موقوف إدارياً' : 'Suspended Member') : ($ar ? 'العضوية مجمدة حالياً' : 'Frozen Membership') }}
                </span>
            @endif

            @if($cardWaLink)
                <a href="{{ $cardWaLink }}" target="_blank" class="btn-control" style="background:#25d366;color:#fff;border-color:#25d366;" title="{{ $ar ? 'إرسال بيانات الكارت للعضو عبر واتساب' : 'Send Card Info via WhatsApp' }}">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>{{ $ar ? 'إرسال عبر واتساب' : 'WhatsApp Pass' }}</span>
                </a>
            @endif

            <a href="{{ route('academy.students.cards.print', ['layout' => $layout, 'theme' => $cardTheme, 'orientation' => $orientation, 'side' => $sideOption]) }}" class="btn-control btn-outline-back" style="color:#60a5fa;border-color:#3b82f6;">
                <i class="fa-solid fa-users-rectangle"></i>
                <span>{{ $ar ? 'طباعة كروت كافة المشتركين' : 'Print All Members' }}</span>
            </a>
        @else
            <span class="meta-info-tag">
                <i class="fa-solid fa-id-card me-1" style="color:#38bdf8"></i>
                {{ $ar ? 'إجمالي البطاقات:' : 'Total Cards:' }} <b style="color:#f8fafc">{{ $targetCards->count() }}</b>
                ({{ $pages->count() }} {{ $ar ? 'ورقة A4' : 'A4 Sheets' }})
            </span>
        @endif
    </div>
</header>

{{-- ── حاوية صفحات الطباعة A4 ───────────────────────────────────────── --}}
<main class="print-pages-container">
    @forelse($pages as $pageIndex => $sheetCards)
        <section class="a4-sheet orient-{{ $orientation }} layout-{{ $layout }}">
            @foreach($sheetCards as $item)
                @php
                    $mbr   = $item['member'];
                    $side  = $item['side']; // 'front' or 'back'

                    $mCode = $mbr->computed_membership_code ?? \App\Support\MembershipCode::make($mbr);
                    $sub   = $mbr->computed_subscription ?? $mbr->subscriptions->sortByDesc('starts_on')->first();
                    $qrUri = $mbr->computed_qr ?? null;
                    $bcSvg = $mbr->computed_barcode ?? null;

                    // توليد QR و Barcode إذا لم يكونا مسبقي الحساب
                    if (!$qrUri) {
                        $qrRes = (new \Endroid\QrCode\Writer\SvgWriter())->write(new \Endroid\QrCode\QrCode(data: $mCode, size: 260, margin: 4));
                        $qrUri = $qrRes->getDataUri();
                    }
                    if (!$bcSvg) {
                        $bcSvg = (new \Picqer\Barcode\BarcodeGeneratorSVG())->getBarcode($mCode, \Picqer\Barcode\BarcodeGeneratorSVG::TYPE_CODE_128, 1.35, 42);
                    }

                    $subStatus = $sub?->status;
                    $isFrozen  = $sub?->is_frozen || str_contains((string)($sub?->notes ?? ''), 'frozen');
                    $startsOn  = $sub?->starts_on?->format('Y-m-d') ?: $mbr->created_at?->format('Y-m-d');
                    $endsOn    = $sub?->ends_on?->format('Y-m-d');

                    $phone = $mbr->phone ?: $mbr->user?->phone;
                    $planName = $sub?->group?->name ?: ($sub?->session_credits_total ? ($sub->session_credits_total . ' ' . ($ar ? 'جلسة' : 'Sessions')) : ($facilityType === 'gym' ? ($ar ? 'عضوية لياقة عامة' : 'General Fitness') : ($ar ? 'اشتراك نشط' : 'Standard')));
                @endphp

                <div class="card-outer-wrap card-is-{{ $orientation }}">
                    {{-- علامات قص الورق بالمسطرة أو المقص --}}
                    <div class="cut-mark cut-mark-tl"></div>
                    <div class="cut-mark cut-mark-tr"></div>
                    <div class="cut-mark cut-mark-bl"></div>
                    <div class="cut-mark cut-mark-br"></div>

                    {{-- ══════════════════════════════════════════════════
                         1. الكارت الأفقي (Landscape)
                         ══════════════════════════════════════════════════ --}}
                    @if($orientation === 'landscape')
                        @if($side === 'front')
                            {{-- الوجه الأمامي الأفقي (VIP Pass) --}}
                            <article class="exec-card card-is-landscape landscape-front theme-{{ $cardTheme }} facility-{{ $facilityType }}">
                                <div class="card-top-stripe"></div>

                                <header class="head-box">
                                    <div class="brand-group">
                                        @if($academy?->logo)
                                            <img src="{{ $academy->logo }}" class="brand-logo-img" alt="Logo">
                                        @else
                                            <div class="brand-logo-alt"><i class="fa-solid fa-dumbbell"></i></div>
                                        @endif
                                        <div class="brand-titles">
                                            <span class="brand-title">{{ $academy?->commercial_name ?: ($ar ? 'المنشأة الرياضية' : 'Sports Facility') }}</span>
                                            <span class="brand-subtitle">{{ $ar ? 'بطاقة العضوية الذكية' : 'Smart Membership Pass' }}</span>
                                        </div>
                                    </div>
                                    <div class="vip-tier-badge">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        <span>{{ $cardTitle }}</span>
                                    </div>
                                </header>

                                <div class="body-box">
                                    {{-- الصورة الشخصية والشارات --}}
                                    <div class="photo-col">
                                        <div class="avatar-frame">
                                            <img class="avatar-img"
                                                 src="{{ $mbr->avatarUrl() }}"
                                                 onerror="this.onerror=null;this.src='{{ $mbr->defaultImageUrl() }}'"
                                                 alt="{{ $mbr->name }}">
                                            <span class="avatar-dot" title="{{ $ar ? 'عضوية معتمدة' : 'Verified Member' }}">
                                                <i class="fa-solid fa-check" style="font-size:1.8mm;color:#fff;"></i>
                                            </span>
                                        </div>
                                        @if($mbr->hasSpecialCare())
                                            <span class="care-alert-pill" title="{{ implode(' | ', $mbr->specialCareSummary()) }}">
                                                ⚠️ {{ $ar ? 'رعاية خاصة' : 'Care Alert' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- تفاصيل العضو (بداية ونهاية الاشتراك المطبوعة) --}}
                                    <div class="details-col">
                                        <h2 class="member-full-name" title="{{ $mbr->name }}">{{ $mbr->name }}</h2>

                                        <div class="quick-meta-grid">
                                            <div class="meta-item">
                                                <span class="meta-lbl"><i class="fa-solid fa-id-badge"></i> {{ $ar ? 'الباقة:' : 'Plan:' }}</span>
                                                <span class="meta-val" title="{{ $planName }}">{{ $planName }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-lbl"><i class="fa-regular fa-calendar-plus"></i> {{ $ar ? 'بداية الاشتراك:' : 'Starts:' }}</span>
                                                <span class="meta-val" dir="ltr">{{ $startsOn ?: '—' }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-lbl"><i class="fa-regular fa-calendar-check"></i> {{ $ar ? 'نهاية الاشتراك:' : 'Ends:' }}</span>
                                                <span class="meta-val" dir="ltr" style="font-weight:900;">{{ $endsOn ?: ($ar ? 'اشتراك مستمر' : 'Ongoing') }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-lbl"><i class="fa-solid fa-phone"></i> {{ $ar ? 'الهاتف:' : 'Phone:' }}</span>
                                                <span class="meta-val" dir="ltr">{{ $phone ?: '—' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- رمز الدخول الذكي للبوابات --}}
                                    <div class="qr-col">
                                        <div class="qr-holder" title="{{ $mCode }}">
                                            <img src="{{ $qrUri }}" alt="Gate QR Pass">
                                        </div>
                                        <span class="qr-sub">
                                            <i class="fa-solid fa-qrcode"></i>
                                            <span>{{ $ar ? 'بوابة الدخول' : 'Gate Pass' }}</span>
                                        </span>
                                    </div>
                                </div>

                                <footer class="foot-box">
                                    <div class="barcode-box" title="{{ $mCode }}">
                                        {!! $bcSvg !!}
                                    </div>
                                    <div class="security-tag">
                                        <span class="sec-title"><i class="fa-solid fa-microchip"></i> Hagzz Pass</span>
                                        <span class="sec-sub">{{ $ar ? 'بوابة إلكترونية ذكية' : 'Gate Ready' }}</span>
                                    </div>
                                </footer>
                            </article>
                        @else
                            {{-- ظهر الكارت الأفقي (Rules & Full Details) --}}
                            <article class="exec-card card-is-landscape landscape-back theme-{{ $cardTheme }} facility-{{ $facilityType }}">
                                <div class="card-top-stripe"></div>

                                <header class="head-box">
                                    <span style="font-weight:900;font-size:2.2mm;">
                                        <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                        {{ $ar ? 'تعليمات وقواعد استخدام البطاقة' : 'Card Usage Terms & Guidelines' }}
                                    </span>
                                    <span style="font-size:1.7mm;font-weight:700;color:#64748b;">
                                        <i class="fa-solid fa-headset me-1 text-primary"></i>{{ $ar ? 'خدمة المشتركين:' : 'Helpdesk:' }} <span dir="ltr">{{ $academy?->phone ?: $academy?->email }}</span>
                                    </span>
                                </header>

                                <div class="body-box">
                                    {{-- ملخص بيانات المشترك والاشتراك (الجانب الأيمن) --}}
                                    <div class="details-summary">
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'اسم العضو:' : 'Member:' }}</span>
                                            <span class="sum-val">{{ $mbr->name }}</span>
                                        </div>
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'الباقة:' : 'Plan:' }}</span>
                                            <span class="sum-val">{{ $planName }}</span>
                                        </div>
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'بداية الاشتراك:' : 'Starts:' }}</span>
                                            <span class="sum-val" dir="ltr">{{ $startsOn ?: '—' }}</span>
                                        </div>
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'نهاية الاشتراك:' : 'Ends:' }}</span>
                                            <span class="sum-val" dir="ltr" style="font-weight:900;color:#0f766e;">{{ $endsOn ?: ($ar ? 'مستمر' : 'Ongoing') }}</span>
                                        </div>
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'الهاتف:' : 'Phone:' }}</span>
                                            <span class="sum-val" dir="ltr">{{ $phone ?: '—' }}</span>
                                        </div>
                                        @if($mbr->guardian_phone)
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'الطوارئ:' : 'Emergency:' }}</span>
                                            <span class="sum-val" dir="ltr">{{ $mbr->guardian_phone }}</span>
                                        </div>
                                        @endif
                                        <div class="sum-row">
                                            <span class="sum-lbl">{{ $ar ? 'الفئة:' : 'Category:' }}</span>
                                            <span class="sum-val">
                                                {{ $mbr->gender === 'female' ? ($ar ? 'أنثى' : 'Female') : ($ar ? 'ذكر' : 'Male') }}
                                                @if($mbr->blood_type) · {{ $mbr->blood_type }} @endif
                                            </span>
                                        </div>
                                        @if($mbr->hasSpecialCare())
                                            <div class="sum-row" style="color:#d97706;font-weight:900;">
                                                <span class="sum-lbl" style="color:#d97706;">{{ $ar ? 'ملاحظة طبية:' : 'Care Note:' }}</span>
                                                <span class="sum-val" title="{{ implode(' | ', $mbr->specialCareSummary()) }}">{{ Str::limit(implode(' | ', $mbr->specialCareSummary()), 26) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- تعليمات المنشأة (الجانب الأيسر) --}}
                                    <ul class="rules-list">
                                        @foreach($facilityRules as $rIndex => $rule)
                                            <li class="card-rule-item">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span>{{ $rule }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <footer class="foot-box">
                                    <div class="barcode-full">
                                        {!! $bcSvg !!}
                                    </div>
                                    <div style="display:flex;align-items:center;gap:1.5mm;">
                                        <span style="font-size:1.5mm;font-weight:800;color:#64748b;">
                                            {{ $ar ? 'توقيع واعتماد الإدارة' : 'Authorized Signature' }}
                                        </span>
                                        <span style="display:inline-block;width:18mm;border-bottom:0.3mm dashed #94a3b8;"></span>
                                    </div>
                                </footer>
                            </article>
                        @endif

                    {{-- ══════════════════════════════════════════════════
                         2. الكارت الرأسي / باج تعليق (Portrait ID Badge)
                         ══════════════════════════════════════════════════ --}}
                    @else
                        @if($side === 'front')
                            {{-- وجه الباج الرأسي (Front Portrait Badge) --}}
                            <article class="exec-card card-is-portrait portrait-front theme-{{ $cardTheme }} facility-{{ $facilityType }}">
                                <div class="lanyard-slot-mark"></div>
                                <div class="card-top-stripe" style="margin-top:1mm;"></div>

                                <header class="head-box">
                                    <div style="display:flex;align-items:center;gap:1.5mm;">
                                        @if($academy?->logo)
                                            <img src="{{ $academy->logo }}" style="width:6mm;height:6mm;object-fit:contain;border-radius:1mm;background:#fff;padding:0.2mm;" alt="Logo">
                                        @endif
                                        <span class="brand-title" style="font-size:2.8mm;font-weight:900;">{{ $academy?->commercial_name ?: ($ar ? 'المنشأة الرياضية' : 'Sports Club') }}</span>
                                    </div>
                                    <span class="vip-tier-badge" style="font-size:1.7mm;padding:0.4mm 1.8mm;">{{ $cardTitle }}</span>
                                </header>

                                <div class="body-box">
                                    <div class="avatar-frame">
                                        <img class="avatar-img"
                                             src="{{ $mbr->avatarUrl() }}"
                                             onerror="this.onerror=null;this.src='{{ $mbr->defaultImageUrl() }}'"
                                             alt="{{ $mbr->name }}">
                                        <span class="avatar-dot" title="{{ $ar ? 'عضوية معتمدة' : 'Verified Member' }}">
                                            <i class="fa-solid fa-check" style="font-size:1.8mm;color:#fff;"></i>
                                        </span>
                                    </div>

                                    <h2 class="member-full-name">{{ $mbr->name }}</h2>

                                    <div class="portrait-dates-box">
                                        <div class="p-date-item">
                                            <span class="p-date-lbl">{{ $ar ? 'البداية:' : 'From:' }}</span>
                                            <span class="p-date-val" dir="ltr">{{ $startsOn ?: '—' }}</span>
                                        </div>
                                        <span class="p-date-sep">➔</span>
                                        <div class="p-date-item">
                                            <span class="p-date-lbl">{{ $ar ? 'النهاية:' : 'To:' }}</span>
                                            <span class="p-date-val" dir="ltr" style="font-weight:900;">{{ $endsOn ?: ($ar ? 'مستمر' : 'Ongoing') }}</span>
                                        </div>
                                    </div>

                                    @if($mbr->hasSpecialCare())
                                        <span class="care-alert-pill" title="{{ implode(' | ', $mbr->specialCareSummary()) }}">
                                            ⚠️ {{ $ar ? 'رعاية خاصة' : 'Care Alert' }}
                                        </span>
                                    @endif

                                    <div class="qr-holder" title="{{ $mCode }}">
                                        <img src="{{ $qrUri }}" alt="Gate QR">
                                    </div>
                                    <span style="font-size:1.6mm;font-weight:800;color:#64748b;">
                                        <i class="fa-solid fa-qrcode"></i> {{ $ar ? 'بوابة الدخول الذكية' : 'Gate Pass' }}
                                    </span>
                                </div>

                                <footer class="foot-box">
                                    <div class="barcode-box" title="{{ $mCode }}">
                                        {!! $bcSvg !!}
                                    </div>
                                    <span style="font-size:1.5mm;font-weight:700;color:#64748b;">
                                        Hagzz Smart Access ID
                                    </span>
                                </footer>
                            </article>
                        @else
                            {{-- ظهر الباج الرأسي (Back Portrait Badge) --}}
                            <article class="exec-card card-is-portrait portrait-back theme-{{ $cardTheme }} facility-{{ $facilityType }}">
                                <div class="lanyard-slot-mark"></div>
                                <div class="card-top-stripe" style="margin-top:1mm;"></div>

                                <header class="head-box">
                                    <span style="font-weight:900;font-size:2.2mm;">
                                        <i class="fa-solid fa-shield-halved me-1"></i>
                                        {{ $ar ? 'شروط وتعليمات العضوية' : 'Membership Terms' }}
                                    </span>
                                </header>

                                <div class="body-box">
                                    @foreach($facilityRules as $rIndex => $rule)
                                        <div class="card-rule-item">
                                            <i class="fa-solid fa-check-circle" style="color:#2563eb;margin-top:0.2mm;"></i>
                                            <span>{{ $rule }}</span>
                                        </div>
                                    @endforeach

                                    <div style="border-top:0.25mm solid #cbd5e1;padding-top:1.5mm;margin-top:auto;text-align:start;display:flex;flex-direction:column;gap:0.8mm;">
                                        <div><b>{{ $ar ? 'الباقة:' : 'Plan:' }}</b> {{ $planName }}</div>
                                        <div><b>{{ $ar ? 'بداية الاشتراك:' : 'Starts:' }}</b> <span dir="ltr">{{ $startsOn ?: '—' }}</span></div>
                                        <div><b>{{ $ar ? 'نهاية الاشتراك:' : 'Ends:' }}</b> <span dir="ltr" style="font-weight:900;">{{ $endsOn ?: ($ar ? 'مستمر' : 'Ongoing') }}</span></div>
                                        <div><b>{{ $ar ? 'الهاتف:' : 'Phone:' }}</b> <span dir="ltr">{{ $phone ?: '—' }}</span></div>
                                        @if($mbr->guardian_phone)
                                            <div><b>{{ $ar ? 'الطوارئ:' : 'Emergency:' }}</b> <span dir="ltr">{{ $mbr->guardian_phone }}</span></div>
                                        @endif
                                    </div>
                                </div>

                                <footer class="foot-box">
                                    <div class="barcode-box" title="{{ $mCode }}">
                                        {!! $bcSvg !!}
                                    </div>
                                    <span style="font-size:1.4mm;font-weight:700;color:#64748b;margin-top:0.5mm;">
                                        {{ $academy?->commercial_name }}
                                    </span>
                                </footer>
                            </article>
                        @endif
                    @endif
                </div>
            @endforeach
        </section>
    @empty
        <div style="text-align:center;padding:60px;background:#111827;color:#f8fafc;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.5)">
            <i class="fa-solid fa-id-card mb-3" style="font-size:48px;color:#64748b"></i>
            <h3 style="margin-bottom:8px">{{ $ar ? 'لا يوجد أعضاء مطابقين للطباعة' : 'No members found to print' }}</h3>
            <p style="color:#94a3b8;margin-bottom:20px">{{ $ar ? 'يرجى مراجعة محددات البحث أو إضافة مشتركين أولاً.' : 'Please adjust filters or add members first.' }}</p>
            <a href="{{ route('academy.students.index') }}" class="btn-control btn-primary-action">{{ $backLabel }}</a>
        </div>
    @endforelse
</main>

</body>
</html>
