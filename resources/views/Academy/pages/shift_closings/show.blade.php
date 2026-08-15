@php
    $ar = app()->getLocale() === 'ar';
    $paper = request('paper', 'pos');
    $pageSize = $paper === 'pos' ? '80mm auto' : 'A4';
    $pageMargin = $paper === 'pos' ? '3mm' : '10mm';
    $money = fn ($v) => number_format((float) $v, 2);

    $diff = (float) $shiftClosing->cash_difference;
    $academyName = $academy?->commercial_name ?: 'Hagzz';

    $waMessage = "📊 *تقرير تقفيل وردية وكاش (Z-Report)*\n"
        . "🏢 *المنشأة:* {$academyName}\n"
        . "🏷️ *الوردية:* {$shiftClosing->shift_title}\n"
        . "👤 *مسؤول الوردية:* {$shiftClosing->closed_by_name}\n"
        . "⏰ *التوقيت:* {$shiftClosing->started_at->format('Y-m-d H:i')} — {$shiftClosing->closed_at->format('H:i')}\n\n"
        . "💵 *كاش النظام:* " . $money($shiftClosing->total_cash_system) . " EGP\n"
        . "💰 *الكاش الفعلي بالدرج:* " . $money($shiftClosing->actual_cash_counted) . " EGP\n"
        . "⚖️ *الفارق:* " . ($diff >= 0 ? '+' : '') . $money($diff) . " EGP\n"
        . "💳 *البطاقات / POS:* " . $money($shiftClosing->total_card_system) . " EGP\n"
        . "📱 *إنستاباي / محافظ:* " . $money($shiftClosing->total_instapay_system) . " EGP\n"
        . "🎁 *الخصومات المعتمدة:* " . $money($shiftClosing->total_discounts_system) . " EGP\n"
        . "🌟 *إجمالي التحصيلات الكلية:* " . $money($shiftClosing->total_collected_system) . " EGP\n\n"
        . "تم الاعتماد والإغلاق بنجاح عبر منصة Hagzz.";

    $waUrl = 'https://api.whatsapp.com/send?text=' . urlencode($waMessage);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Z-Report #{{ $shiftClosing->id }} - {{ $shiftClosing->shift_title }}</title>
    <style>
        @page { size: {{ $pageSize }}; margin: {{ $pageMargin }}; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #edf1f7; color: #172033; font: 12px Tahoma, Arial, sans-serif; }
        .toolbar { position: sticky; top: 0; z-index: 5; display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 8px; padding: 12px; background: #172033; }
        .toolbar a, .toolbar button { border: 1px solid #536077; background: #fff; color: #172033; border-radius: 8px; padding: 9px 14px; font-weight: 700; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; }
        .toolbar .active { background: #19a974; color: #fff; border-color: #19a974; }
        .toolbar .btn-wa { background: #25D366; color: #fff; border-color: #25D366; }
        .toolbar .btn-image { background: #0284c7; color: #fff; border-color: #0284c7; }
        .toolbar .btn-print { background: #0e5a3f; color: #fff; border-color: #0e5a3f; }
        .sheet { width: 80mm; min-height: 0; margin: 15px auto; padding: 4mm; background: #fff; box-shadow: 0 8px 30px #19233a24; }
        .paper-a4 .sheet { width: 210mm; min-height: 277mm; padding: 12mm; }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 8px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #111; }
        .header p { margin: 3px 0 0; color: #555; font-size: 11px; }
        .info-table, .totals-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 3px 0; font-size: 11px; }
        .info-table td:last-child { text-align: end; font-weight: 700; }
        .totals-table th { background: #f1f5f9; padding: 5px; text-align: start; font-size: 11px; border-bottom: 1px solid #ddd; }
        .totals-table td { padding: 5px; border-bottom: 1px dashed #eee; font-size: 11px; }
        .totals-table td:last-child { text-align: end; font-weight: 700; }
        .grand-total { border-top: 2px solid #111; border-bottom: 2px solid #111; font-size: 13px !important; font-weight: bold; background: #f8fafc; }
        .diff-box { padding: 6px; border-radius: 4px; text-align: center; margin: 8px 0; font-weight: bold; }
        .diff-ok { background: #dcfce7; color: #166534; }
        .diff-surplus { background: #e0f2fe; color: #0369a1; }
        .diff-shortage { background: #fee2e2; color: #b91c1c; }
        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 12px; border-top: 1px dashed #ccc; padding-top: 6px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="paper-{{ $paper }}">
    <nav class="toolbar">
        <a class="{{ $paper==='pos'?'active':'' }}" href="{{ request()->fullUrlWithQuery(['paper'=>'pos']) }}">POS 80mm</a>
        <a class="{{ $paper==='a4'?'active':'' }}" href="{{ request()->fullUrlWithQuery(['paper'=>'a4']) }}">A4 Print</a>

        <a href="{{ $waUrl }}" target="_blank" class="btn-wa">
            <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 448 512">
                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
            {{ $ar ? 'إرسال للإدارة واتساب' : 'Send via WhatsApp' }}
        </a>

        <button type="button" class="btn-image" onclick="saveAsImage()">
            📸 {{ $ar ? 'حفظ كصورة' : 'Save Image' }}
        </button>

        <button onclick="window.print()" class="btn-print">{{ $ar ? 'طباعة الإيصال' : 'Print Z-Report' }}</button>
        <a href="{{ route('academy.shift-closings.index') }}">{{ $ar ? 'العودة للورديات' : 'Back' }}</a>
    </nav>

    <main class="sheet" id="reportSheet">
        <div class="header">
            <h1>{{ $academyName }}</h1>
            <p>{{ $ar ? 'تقرير تقفيل وردية وكاش (Z-REPORT)' : 'Shift Closing Cash Report (Z-Report)' }}</p>
            <p><strong>#{{ $shiftClosing->id }} - {{ $shiftClosing->shift_title }}</strong></p>
        </div>

        <table class="info-table">
            <tr>
                <td>{{ $ar ? 'مسؤول الوردية:' : 'Cashier:' }}</td>
                <td>{{ $shiftClosing->closed_by_name }}</td>
            </tr>
            <tr>
                <td>{{ $ar ? 'بداية الوردية:' : 'Started:' }}</td>
                <td>{{ $shiftClosing->started_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td>{{ $ar ? 'إغلاق الوردية:' : 'Closed:' }}</td>
                <td>{{ $shiftClosing->closed_at->format('Y-m-d H:i') }}</td>
            </tr>
            @if($shiftClosing->next_shift_receiver)
                <tr>
                    <td>{{ $ar ? 'مستلم الوردية:' : 'Next Receiver:' }}</td>
                    <td>{{ $shiftClosing->next_shift_receiver }}</td>
                </tr>
            @endif
        </table>

        <div class="diff-box {{ $diff == 0 ? 'diff-ok' : ($diff > 0 ? 'diff-surplus' : 'diff-shortage') }}">
            @if($diff == 0)
                {{ $ar ? '✅ الكاش الفعلي مطابق تماماً لكاش النظام' : '✅ Cash Drawer Perfectly Balanced' }}
            @elseif($diff > 0)
                {{ $ar ? '➕ يوجد زيادة في الكاش: +' . $money($diff) . ' EGP' : '➕ Cash Surplus: +' . $money($diff) . ' EGP' }}
            @else
                {{ $ar ? '⚠️ يوجد عجز في الكاش: ' . $money($diff) . ' EGP' : '⚠️ Cash Shortage: ' . $money($diff) . ' EGP' }}
            @endif
        </div>

        <table class="totals-table">
            <thead>
                <tr>
                    <th>{{ $ar ? 'طريقة التحصيل / البند' : 'Payment Method / Item' }}</th>
                    <th style="text-align:end;">{{ $ar ? 'المبلغ' : 'Amount' }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>💵 {{ $ar ? 'كاش النظام المسجل' : 'System Cash' }}</td>
                    <td>{{ $money($shiftClosing->total_cash_system) }} EGP</td>
                </tr>
                <tr style="background:#f0fdf4; font-weight:bold;">
                    <td>💰 {{ $ar ? 'الكاش الفعلي المستلم بالدرج' : 'Actual Cash Counted' }}</td>
                    <td>{{ $money($shiftClosing->actual_cash_counted) }} EGP</td>
                </tr>
                <tr>
                    <td>💳 {{ $ar ? 'البطاقات / نقاط البيع (POS)' : 'Card / POS' }}</td>
                    <td>{{ $money($shiftClosing->total_card_system) }} EGP</td>
                </tr>
                <tr>
                    <td>📱 {{ $ar ? 'إنستا باي (InstaPay)' : 'InstaPay' }}</td>
                    <td>{{ $money($shiftClosing->total_instapay_system) }} EGP</td>
                </tr>
                @if($shiftClosing->total_fawry_system > 0)
                    <tr>
                        <td>⚡ {{ $ar ? 'فوري (Fawry)' : 'Fawry' }}</td>
                        <td>{{ $money($shiftClosing->total_fawry_system) }} EGP</td>
                    </tr>
                @endif
                @if($shiftClosing->total_bank_system > 0)
                    <tr>
                        <td>🏦 {{ $ar ? 'تحويل بنكي' : 'Bank Transfer' }}</td>
                        <td>{{ $money($shiftClosing->total_bank_system) }} EGP</td>
                    </tr>
                @endif
                @if($shiftClosing->total_other_system > 0)
                    <tr>
                        <td>🔄 {{ $ar ? 'طرق دفع أخرى' : 'Other Methods' }}</td>
                        <td>{{ $money($shiftClosing->total_other_system) }} EGP</td>
                    </tr>
                @endif
                @if($shiftClosing->total_discounts_system > 0)
                    <tr style="color:#7e22ce;">
                        <td>🎁 {{ $ar ? 'الخصومات والتسويات المعتمدة' : 'Approved Discounts' }}</td>
                        <td>- {{ $money($shiftClosing->total_discounts_system) }} EGP</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>🌟 {{ $ar ? 'إجمالي تحصيلات الوردية' : 'Total Shift Collections' }}</td>
                    <td>{{ $money($shiftClosing->total_collected_system) }} EGP</td>
                </tr>
            </tbody>
        </table>

        @if($shiftClosing->notes)
            <div style="margin-top:8px; padding:6px; background:#f9fafb; border:1px solid #eee; border-radius:4px;">
                <b>{{ $ar ? 'ملاحظات:' : 'Notes:' }}</b> {{ $shiftClosing->notes }}
            </div>
        @endif

        <div class="footer">
            <p>{{ $ar ? 'تم إنشاء واعتماد هذا التقرير إلكترونياً عبر منصة Hagzz' : 'Electronically generated by Hagzz Platform' }}</p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </main>

    <script>
        function saveAsImage() {
            const sheet = document.getElementById('reportSheet');
            const btn = document.querySelector('.btn-image');
            const orig = btn.innerHTML;
            btn.innerHTML = '⏳ {{ $ar ? "جاري التجهيز..." : "Generating..." }}';

            html2canvas(sheet, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Z-Report-Shift-{{ $shiftClosing->id }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '✅ {{ $ar ? "تم التنزيل!" : "Downloaded!" }}';
                setTimeout(() => { btn.innerHTML = orig; }, 2000);
            }).catch(e => {
                alert('Error generating image');
                btn.innerHTML = orig;
            });
        }
    </script>
</body>
</html>
