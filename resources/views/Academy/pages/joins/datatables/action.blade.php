@php
    $ar = app()->getLocale() === 'ar';
    $invoice = $join->invoice;
    $invId = $invoice?->id ?: $join->invoice_id;
    $student = $join->student ?: $join->user;
    $phone = $student?->phone ?: ($join->phone ?: null);
    
    // Clean phone for WhatsApp
    $rawPhone = (string) $phone;
    $digits = preg_replace('/\D+/', '', $rawPhone);
    if (str_starts_with($digits, '00')) {
        $cleanPhone = substr($digits, 2);
    } elseif (str_starts_with($digits, '01') && strlen($digits) === 11) {
        $cleanPhone = '20' . substr($digits, 1);
    } elseif (str_starts_with($digits, '05') && strlen($digits) === 10) {
        $cleanPhone = '966' . substr($digits, 1);
    } elseif (str_starts_with($digits, '0') && strlen($digits) > 7) {
        $cleanPhone = substr($digits, 1);
    } else {
        $cleanPhone = $digits;
    }

    $buyerName = $student?->name ?: ($join->name ?: ($ar ? 'العميل' : 'Member'));
    $itemName = $join->training?->name ?: ($ar ? 'باقة الاشتراك' : 'Membership Plan');
    $totalAmount = (float) ($invoice?->amount ?: ($join->price ?: 0));
    $paidAmount = (float) ($invoice?->collected_amount ?: ($join->paid_amount ?: 0));
    $remainingAmount = max(0, $totalAmount - $paidAmount);
    $currency = $join->training?->academy?->currency_symbol ?: ($ar ? 'ر.س' : 'SAR');
    $publicInvUrl = $invId ? route('invoices.public.view', ['type' => 'booking', 'id' => $invId]) : '';

    if ($ar) {
        $waMsg = "مرحباً بك كابتن *{$buyerName}* 🌟\n"
            . "يسعدنا تأكيد تسجيل اشتراكك:\n\n"
            . "📋 *الباقة:* {$itemName}\n"
            . "🔢 *رقم الفاتورة:* #" . ($invoice?->order_number ?: $join->id) . "\n"
            . "💰 *الإجمالي:* " . number_format($totalAmount, 2) . " {$currency}\n"
            . "✅ *المسدد:* " . number_format($paidAmount, 2) . " {$currency}\n"
            . "⏳ *المتبقي:* " . number_format($remainingAmount, 2) . " {$currency}\n\n"
            . ($publicInvUrl ? "🔗 *رابط الفاتورة الإلكترونية الموحدة:* \n{$publicInvUrl}\n\n" : "")
            . "نتمنى لك تدريباً ممتعاً ومليئاً بالنشاط! 💪🔥";
    } else {
        $waMsg = "Hello *{$buyerName}* 🌟\n"
            . "Your booking is confirmed:\n"
            . "📋 *Plan:* {$itemName}\n"
            . "🔢 *Invoice #:* #" . ($invoice?->order_number ?: $join->id) . "\n"
            . "💰 *Total:* " . number_format($totalAmount, 2) . " {$currency}\n"
            . "✅ *Paid:* " . number_format($paidAmount, 2) . " {$currency}\n"
            . ($publicInvUrl ? "🔗 *Invoice:* \n{$publicInvUrl}\n" : "");
    }
    $waUrl = 'https://api.whatsapp.com/send?' . ($cleanPhone ? 'phone=' . $cleanPhone . '&' : '') . 'text=' . urlencode($waMsg);
    $printA4Url = $invId ? route('academy.invoices.bookings.print', ['invoice' => $invId, 'paper' => 'a4']) : null;
    $printPosUrl = $invId ? route('academy.invoices.bookings.print', ['invoice' => $invId, 'paper' => 'pos']) : null;
    $detailsUrl = route('academy.report.view-booking-details', $join->id);
    $studentId = $join->academy_student_id ?: ($join->student?->id ?: null);
    if (!$studentId && $join->user_id) {
        $academyId = (int) (auth('academy')->user()?->academy_id ?: auth('academy')->id());
        $studentId = \App\Models\AcademyStudent::where('user_id', $join->user_id)
            ->where('academy_id', $academyId)
            ->value('id');
    }
@endphp

<div class="d-inline-flex align-items-center gap-1">
    <!-- View Details -->
    <a href="{{ $detailsUrl }}" 
       class="btn btn-sm btn-outline-primary px-2 py-1 rounded-2 shadow-xs" 
       style="font-size: 12px; min-width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;"
       title="{{ $ar ? 'عرض التفاصيل' : 'View Details' }}"
       data-bs-toggle="tooltip">
        <i class="fa-solid fa-eye"></i>
    </a>

    <!-- Print A4 Invoice -->
    @if($printA4Url)
    <a href="{{ $printA4Url }}" 
       target="_blank" 
       class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-2 shadow-xs" 
       style="font-size: 12px; min-width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;"
       title="{{ $ar ? 'طباعة الفاتورة A4' : 'Print A4' }}"
       data-bs-toggle="tooltip">
        <i class="fa-solid fa-print"></i>
    </a>
    @endif

    <!-- WhatsApp Share -->
    @if($cleanPhone)
    <a href="{{ $waUrl }}" 
       target="_blank" 
       class="btn btn-sm btn-success px-2 py-1 rounded-2 shadow-xs text-white" 
       style="background: #25D366; border-color: #25D366; font-size: 12px; min-width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;"
       title="{{ $ar ? 'إرسال الفاتورة عبر واتساب' : 'WhatsApp' }}"
       data-bs-toggle="tooltip">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    @endif

    <!-- Dropdown More Options -->
    <div class="dropdown d-inline-block position-relative">
        <button class="btn btn-sm btn-light border px-2 py-1 rounded-2 shadow-xs" 
                type="button" 
                data-bs-toggle="dropdown" 
                data-bs-boundary="viewport"
                data-bs-popper-config='{"strategy":"fixed"}'
                aria-expanded="false"
                style="font-size: 12px; min-width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;"
                title="{{ $ar ? 'المزيد من الإجراءات' : 'More' }}">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 py-1.5" style="font-size: 13px; min-width: 220px; z-index: 1080;">
            <li class="px-3 py-1.5 border-bottom mb-1 bg-light-subtle">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 10.5px;">{{ $ar ? 'إجراءات الحجز والفاتورة' : 'Actions' }}</span>
            </li>
            <li>
                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ $detailsUrl }}">
                    <i class="fa-solid fa-folder-open text-primary" style="width: 18px;"></i>
                    <span>{{ $ar ? 'تفاصيل الحجز والسداد' : 'Full Details' }}</span>
                </a>
            </li>
            @if($printA4Url)
            <li>
                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ $printA4Url }}" target="_blank">
                    <i class="fa-solid fa-file-pdf text-danger" style="width: 18px;"></i>
                    <span>{{ $ar ? 'طباعة فاتورة A4 موحدة' : 'Unified A4 Invoice' }}</span>
                </a>
            </li>
            @endif
            @if($printPosUrl)
            <li>
                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ $printPosUrl }}" target="_blank">
                    <i class="fa-solid fa-receipt text-success" style="width: 18px;"></i>
                    <span>{{ $ar ? 'إيصال كاشير حراري (POS 80mm)' : 'Thermal Receipt (POS)' }}</span>
                </a>
            </li>
            @endif
            @if($studentId)
            <li>
                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ route('academy.students.card', $studentId) }}" target="_blank">
                    <i class="fa-solid fa-id-card text-info" style="width: 18px;"></i>
                    <span>{{ $ar ? 'كارت العضوية الرقمي' : 'Digital Member Card' }}</span>
                </a>
            </li>
            @endif
            <li>
                <hr class="dropdown-divider my-1">
            </li>
            <li>
                <a class="dropdown-item py-2 fw-semibold d-flex align-items-center gap-2" href="{{ route('academy.report.export-booking-file', $join->id) }}">
                    <i class="fa-solid fa-file-excel text-success" style="width: 18px;"></i>
                    <span>{{ $ar ? 'تصدير بيانات الحجز (Excel)' : 'Export Excel' }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
