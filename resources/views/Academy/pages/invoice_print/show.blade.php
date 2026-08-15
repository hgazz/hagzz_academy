@php
    $ar = app()->getLocale() === 'ar';
    $l = $ar ? [
        'title'=>'فاتورة إلكترونية','copy'=>'نسخة العميل','no'=>'رقم الفاتورة','date'=>'تاريخ الإصدار','due'=>'تاريخ الاستحقاق','printed'=>'تاريخ ووقت الطباعة',
        'seller'=>'مقدم الخدمة','buyer'=>'العميل / المستلم','taxno'=>'الرقم الضريبي','phone'=>'الهاتف','email'=>'البريد','address'=>'العنوان',
        'desc'=>'البيان','qty'=>'الكمية','price'=>'السعر','subtotal'=>'قبل الخصم','discount'=>'الخصم','tax'=>'الضريبة','total'=>'الإجمالي',
        'paid'=>'المدفوع','balance'=>'المتبقي','status'=>'حالة الدفع','method'=>'طريقة الدفع','notes'=>'ملاحظات','print'=>'طباعة / حفظ PDF',
        'send_whatsapp'=>'إرسال عبر واتساب',
        'save_image'=>'حفظ كصورة (PNG)',
        'signed'=>'صادرة وموقعة إلكترونيًا عبر منصة حجز الرقمية للتكنولوجيا الرياضية','sigref'=>'مرجع التوقيع الرقمي','footer'=>'تم إنشاء هذه الفاتورة واعتمادها إلكترونيًا بواسطة منصة حجز.',
    ] : [
        'title'=>'Electronic Invoice','copy'=>'Customer copy','no'=>'Invoice no.','date'=>'Issue date','due'=>'Due date','printed'=>'Printed at',
        'seller'=>'Service provider','buyer'=>'Customer / Recipient','taxno'=>'Tax number','phone'=>'Phone','email'=>'Email','address'=>'Address',
        'desc'=>'Description','qty'=>'Qty','price'=>'Price','subtotal'=>'Subtotal','discount'=>'Discount','tax'=>'Tax','total'=>'Total',
        'paid'=>'Paid','balance'=>'Balance','status'=>'Payment status','method'=>'Payment method','notes'=>'Notes','print'=>'Print / Save PDF',
        'send_whatsapp'=>'Send via WhatsApp',
        'save_image'=>'Save as Image (PNG)',
        'signed'=>'Digitally issued and signed through Hagzz Digital Sports Technology Platform','sigref'=>'Digital signature reference','footer'=>'This invoice was electronically generated and approved by Hagzz Platform.',
    ];
    $types = $ar ? ['booking'=>'فاتورة حجز تدريب','student_subscription'=>'فاتورة اشتراك طالب','venue_booking'=>'فاتورة حجز ملعب','platform_subscription'=>'فاتورة اشتراك منصة Hagzz'] : ['booking'=>'Training booking invoice','student_subscription'=>'Student subscription invoice','venue_booking'=>'Venue booking invoice','platform_subscription'=>'Hagzz platform subscription invoice'];
    $statuses = $ar ? ['paid'=>'مدفوعة','partial'=>'مدفوعة جزئيًا','unpaid'=>'غير مدفوعة','issued'=>'مستحقة','overdue'=>'متأخرة','void'=>'ملغاة','cancelled'=>'ملغاة','draft'=>'مسودة'] : ['paid'=>'Paid','partial'=>'Partially paid','unpaid'=>'Unpaid','issued'=>'Due','overdue'=>'Overdue','void'=>'Void','cancelled'=>'Cancelled','draft'=>'Draft'];
    $money = fn ($value) => number_format((float) $value, 2);
    $pageSize = $paper === 'pos' ? '80mm auto' : strtoupper($paper);
    $pageMargin = $paper === 'pos' ? '3mm' : ($paper === 'a5' ? '8mm' : '10mm');

    // WhatsApp Phone Formatting & Message Generation
    $rawPhone = (string) ($document['buyer']['phone'] ?? '');
    $digits = preg_replace('/\D+/', '', $rawPhone);
    if (str_starts_with($digits, '00')) {
        $cleanPhone = substr($digits, 2);
    } elseif (str_starts_with($digits, '01') && strlen($digits) === 11) { // Egypt
        $cleanPhone = '20' . substr($digits, 1);
    } elseif (str_starts_with($digits, '05') && strlen($digits) === 10) { // Saudi
        $cleanPhone = '966' . substr($digits, 1);
    } elseif (str_starts_with($digits, '0') && strlen($digits) > 7) {
        $cleanPhone = substr($digits, 1);
    } else {
        $cleanPhone = $digits;
    }

    $typeLabel = $types[$document['type']] ?? $l['title'];
    $sellerName = $document['seller']['name'] ?: 'Hagzz';
    $buyerName = $document['buyer']['name'] ?: ($ar ? 'عميلنا العزيز' : 'Valued Customer');
    $invoiceNumber = $document['number'];
    $totalFormatted = $money($document['total']) . ' ' . $document['currency'];
    $paidFormatted = $money($document['paid']) . ' ' . $document['currency'];
    $balanceFormatted = $money($document['balance']) . ' ' . $document['currency'];
    $publicInvoiceUrl = $document['public_url'] ?? request()->fullUrl();
    $lineSummary = collect($document['lines'])->pluck('description')->implode(' | ');

    if ($ar) {
        $waMessage = "مرحباً *{$buyerName}*،\n"
            . "مرفق فاتورتكم الإلكترونية من *{$sellerName}*:\n\n"
            . "📄 *نوع الفاتورة:* {$typeLabel}\n"
            . "🔢 *رقم الفاتورة:* #{$invoiceNumber}\n"
            . "🏷️ *البيان:* {$lineSummary}\n"
            . "💰 *الإجمالي:* {$totalFormatted}\n"
            . "✅ *المدفوع:* {$paidFormatted}\n"
            . "⏳ *المتبقي:* {$balanceFormatted}\n\n"
            . "🔗 *رابط عرض وتحميل الفاتورة مباشرة:* \n{$publicInvoiceUrl}\n\n"
            . "شكراً لاختياركم لنا! 🌟";
    } else {
        $waMessage = "Hello *{$buyerName}*,\n"
            . "Here is your electronic invoice from *{$sellerName}*:\n\n"
            . "📄 *Invoice Type:* {$typeLabel}\n"
            . "🔢 *Invoice No:* #{$invoiceNumber}\n"
            . "🏷️ *Description:* {$lineSummary}\n"
            . "💰 *Total:* {$totalFormatted}\n"
            . "✅ *Paid:* {$paidFormatted}\n"
            . "⏳ *Balance:* {$balanceFormatted}\n\n"
            . "🔗 *View & Download Invoice:* \n{$publicInvoiceUrl}\n\n"
            . "Thank you for choosing us! 🌟";
    }

    $waUrl = 'https://api.whatsapp.com/send?' . ($cleanPhone ? 'phone=' . $cleanPhone . '&' : '') . 'text=' . urlencode($waMessage);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $ar ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $l['title'] }} {{ $document['number'] }}</title>
    <style>
        @page { size: {{ $pageSize }}; margin: {{ $pageMargin }}; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #edf1f7; color: #172033; font: 13px Tahoma, Arial, sans-serif; }
        .toolbar { position: sticky; top: 0; z-index: 5; display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 8px; padding: 12px; background: #172033; }
        .toolbar a, .toolbar button { border: 1px solid #536077; background: #fff; color: #172033; border-radius: 8px; padding: 9px 14px; font-weight: 700; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; transition: all 0.2s ease; }
        .toolbar .active { background: #19a974; color: #fff; border-color: #19a974; }
        .toolbar .btn-wa { background: #25D366; color: #fff; border-color: #25D366; }
        .toolbar .btn-wa:hover { background: #1eb956; border-color: #1eb956; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4); }
        .toolbar .btn-image { background: #0284c7; color: #fff; border-color: #0284c7; }
        .toolbar .btn-image:hover { background: #0369a1; border-color: #0369a1; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.4); }
        .toolbar .btn-print { background: #0e5a3f; color: #fff; border-color: #0e5a3f; }
        .toolbar .btn-print:hover { background: #0b402f; color: #fff; }
        .sheet { width: 210mm; min-height: 277mm; margin: 18px auto; padding: 12mm; background: #fff; box-shadow: 0 8px 30px #19233a24; }
        .paper-a5 .sheet { width: 148mm; min-height: 190mm; padding: 8mm; }
        .head { display: flex; justify-content: space-between; gap: 16px; padding-bottom: 16px; border-bottom: 3px solid #19a974; }
        .head-brand { display: flex; align-items: center; gap: 12px; }
        .issuer-logo { width: 66px; height: 66px; object-fit: contain; }
        .head h1 { margin: 0; color: #13254a; font-size: 25px; }
        .head p, .meta span { color: #64748b; }
        .meta { text-align: end; }
        .meta strong, .meta span { display: block; margin: 3px; }
        .badge { display: inline-block !important; padding: 5px 10px; border-radius: 18px; background: #fff2cc; color: #805500; font-weight: 700; }
        .badge.paid { background: #daf8e9; color: #087443; }
        .badge.cancelled, .badge.void, .badge.overdue { background: #ffe2e2; color: #a51e1e; }
        .dates { display: flex; flex-wrap: wrap; gap: 15px; margin: 14px 0; }
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; margin-bottom: 18px; }
        .party { border: 1px solid #dfe5ee; border-radius: 9px; padding: 12px; }
        .party-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .party-logo { width: 46px; height: 46px; object-fit: contain; border-radius: 8px; }
        .party h2 { margin: 0; color: #19a974; font-size: 12px; }
        .party strong { display: block; font-size: 15px; margin-top: 3px; }
        .party-data { color: #596579; line-height: 1.6; }
        .items, .totals { width: 100%; border-collapse: collapse; }
        .items th { background: #13254a; color: #fff; padding: 9px; text-align: start; }
        .items td { padding: 10px 9px; border-bottom: 1px solid #e4e9f1; }
        .num { text-align: end !important; white-space: nowrap; }
        .totals { width: 48%; margin: 17px 0 0 auto; }
        .totals td { padding: 6px; border-bottom: 1px solid #e4e9f1; }
        .totals td:last-child { text-align: end; font-weight: 700; }
        .grand td { font-size: 16px; border-top: 2px solid #19a974; }
        .payment { margin-top: 16px; padding: 11px; background: #f3f7fb; border-radius: 8px; display: flex; justify-content: space-between; }
        .notes { margin-top: 14px; padding-top: 10px; border-top: 1px dashed #aaa; }
        .signature { display: flex; align-items: center; gap: 12px; margin-top: 18px; padding: 12px; border: 1px solid #b9e5d1; background: #f2fbf7; border-radius: 10px; }
        .signature img { width: 54px; height: 54px; object-fit: contain; }
        .signature strong, .signature small { display: block; }
        .signature strong { color: #087443; }
        .signature small { margin-top: 4px; color: #596579; }
        .footer { text-align: center; margin-top: 12px; color: #7b8798; font-size: 11px; }
        .paper-pos { background: #fff; font-size: 11px; }
        .paper-pos .sheet { width: 80mm; min-height: 0; margin: 0 auto; padding: 3mm; box-shadow: none; }
        .paper-pos .head, .paper-pos .head-brand { display: block; text-align: center; }
        .paper-pos .issuer-logo { width: 48px; height: 48px; }
        .paper-pos .meta { text-align: center; margin-top: 7px; }
        .paper-pos .dates, .paper-pos .parties, .paper-pos .payment { display: block; }
        .paper-pos .dates span, .paper-pos .payment span { display: block; margin: 3px 0; }
        .paper-pos .party { border: 0; border-bottom: 1px dashed #999; border-radius: 0; padding: 7px 0; }
        .paper-pos .party-heading { justify-content: center; }
        .paper-pos .party-logo { width: 38px; height: 38px; }
        .paper-pos .items th { background: #fff; color: #111; border-bottom: 1px solid #111; }
        .paper-pos .items th, .paper-pos .items td { padding: 5px 2px; }
        .paper-pos .items th:nth-child(2), .paper-pos .items td:nth-child(2) { display: none; }
        .paper-pos .totals { width: 100%; margin-top: 8px; }
        .paper-pos .totals td { padding: 4px 2px; }
        .paper-pos .signature { display: block; text-align: center; padding: 7px; }
        .paper-pos .signature img { width: 40px; height: 40px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
        }
        .issuer-logo, .party-logo, .signature img { border-radius: 50%; border: 1px solid #dfe5ee; background: #fff; padding: 3px; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="paper-{{ $paper }}">
    <nav class="toolbar">
        @foreach(['a4'=>'A4','a5'=>'A5','pos'=>'POS 80mm'] as $key=>$text)
            <a class="{{ $paper===$key?'active':'' }}" href="{{ request()->fullUrlWithQuery(['paper'=>$key]) }}">{{ $text }}</a>
        @endforeach

        <a href="{{ $waUrl }}" target="_blank" class="btn-wa" title="{{ $l['send_whatsapp'] }}">
            <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 448 512">
                <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
            {{ $l['send_whatsapp'] }}
        </a>

        <button type="button" class="btn-image" onclick="saveAsImage()" title="{{ $l['save_image'] }}">
            <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 512 512">
                <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l336 0c8.9 0 17.1-4.9 21.3-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/>
            </svg>
            {{ $l['save_image'] }}
        </button>

        <button onclick="window.print()" class="btn-print">{{ $l['print'] }}</button>
    </nav>

    <main class="sheet" id="invoiceSheet">
        <header class="head">
            <div class="head-brand">
                @if(!empty($document['seller']['logo']))
                    <img class="issuer-logo" src="{{ $document['seller']['logo'] }}" alt="{{ $document['seller']['name'] }}" onerror="this.style.display='none'">
                @endif
                <div>
                    <h1>{{ $l['title'] }}</h1>
                    <p>{{ $types[$document['type']] ?? $l['title'] }} · {{ $l['copy'] }}</p>
                </div>
            </div>
            <div class="meta">
                <strong>#{{ $document['number'] }}</strong>
                <span>{{ $l['status'] }}</span>
                <span class="badge {{ $document['status'] }}">{{ $statuses[$document['status']] ?? $document['status'] }}</span>
            </div>
        </header>

        <div class="dates">
            <span>{{ $l['no'] }}: <b>{{ $document['number'] }}</b></span>
            <span>{{ $l['date'] }}: <b>{{ optional($document['issued_at'] ?? null)->format('Y-m-d') ?: '-' }}</b></span>
            @if(!empty($document['due_at']))
                <span>{{ $l['due'] }}: <b>{{ optional($document['due_at'])->format('Y-m-d') }}</b></span>
            @endif
            <span>{{ $l['printed'] }}: <b>{{ $document['printed_at']->format('Y-m-d H:i:s') }}</b></span>
        </div>

        <section class="parties">
            @foreach(['seller','buyer'] as $p)
                <div class="party">
                    <div class="party-heading">
                        @if(!empty($document[$p]['logo']))
                            <img class="party-logo" src="{{ $document[$p]['logo'] }}" alt="{{ $document[$p]['name'] }}" onerror="this.style.display='none'">
                        @endif
                        <div>
                            <h2>{{ $l[$p] }}</h2>
                            <strong>{{ $document[$p]['name'] ?: '-' }}</strong>
                        </div>
                    </div>
                    @foreach(['taxNumber'=>'taxno','phone'=>'phone','email'=>'email','address'=>'address'] as $field=>$label)
                        @if(!empty($document[$p][$field]))
                            <div class="party-data">{{ $l[$label] }}: {{ $document[$p][$field] }}</div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </section>

        <table class="items">
            <thead>
                <tr>
                    <th>{{ $l['desc'] }}</th>
                    <th class="num">{{ $l['qty'] }}</th>
                    <th class="num">{{ $l['price'] }}</th>
                    <th class="num">{{ $l['total'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($document['lines'] as $line)
                    <tr>
                        <td>{{ $line['description'] }}</td>
                        <td class="num">{{ $line['quantity'] }}</td>
                        <td class="num">{{ $money($line['unit_price']) }}</td>
                        <td class="num">{{ $money($line['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>{{ $l['subtotal'] }}</td>
                <td>{{ $money($document['subtotal']) }} {{ $document['currency'] }}</td>
            </tr>
            @if(($document['discount'] ?? 0) > 0)
                <tr>
                    <td>{{ $l['discount'] }}</td>
                    <td>- {{ $money($document['discount']) }} {{ $document['currency'] }}</td>
                </tr>
            @endif
            @if(($document['tax'] ?? 0) > 0)
                <tr>
                    <td>{{ $l['tax'] }} @if(isset($document['tax_rate']))({{ $money($document['tax_rate']) }}%)@endif</td>
                    <td>{{ $money($document['tax']) }} {{ $document['currency'] }}</td>
                </tr>
            @endif
            <tr class="grand">
                <td>{{ $l['total'] }}</td>
                <td>{{ $money($document['total']) }} {{ $document['currency'] }}</td>
            </tr>
            <tr>
                <td>{{ $l['paid'] }}</td>
                <td>{{ $money($document['paid']) }} {{ $document['currency'] }}</td>
            </tr>
            <tr>
                <td>{{ $l['balance'] }}</td>
                <td>{{ $money($document['balance']) }} {{ $document['currency'] }}</td>
            </tr>
        </table>

        <div class="payment">
            <span>{{ $l['status'] }}: <b>{{ $statuses[$document['status']] ?? $document['status'] }}</b></span>
            @if(!empty($document['payment_method']))
                <span>{{ $l['method'] }}: <b>{{ $document['payment_method'] }}</b></span>
            @endif
        </div>

        @if(!empty($document['notes']))
            <div class="notes">
                <b>{{ $l['notes'] }}:</b> {{ $document['notes'] }}
            </div>
        @endif

        <div class="signature">
            <img src="{{ $document['platform_logo'] }}" alt="Hagzz">
            <div>
                <strong>{{ $l['signed'] }}</strong>
                <small>{{ $l['sigref'] }}: {{ $document['signature_reference'] }}</small>
            </div>
        </div>

        <footer class="footer">{{ $l['footer'] }}</footer>
    </main>

    <script>
        function saveAsImage() {
            const sheet = document.getElementById('invoiceSheet');
            const btn = document.querySelector('.btn-image');
            const origHtml = btn.innerHTML;
            btn.innerHTML = '⏳ {{ $ar ? "جاري التجهيز..." : "Generating..." }}';
            btn.disabled = true;

            html2canvas(sheet, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Invoice-{{ $document['number'] }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '✅ {{ $ar ? "تم التنزيل!" : "Downloaded!" }}';
                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.disabled = false;
                }, 2500);
            }).catch(err => {
                console.error('html2canvas error:', err);
                alert('{{ $ar ? "حدث خطأ أثناء تنزيل الصورة، يرجى استخدام زر الطباعة وحفظها كملف PDF." : "Error generating image. Please use Print to save as PDF." }}');
                btn.innerHTML = origHtml;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
