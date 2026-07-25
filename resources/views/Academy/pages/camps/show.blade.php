@extends('Academy.Layouts.master')

@php
    $isArabic = app()->getLocale() === 'ar';
    $occupancyPct = $camp->capacity > 0 ? min(100, round(($camp->participants->count() / $camp->capacity) * 100)) : 0;
@endphp

@section('title', $camp->title)

@section('content')
<div class="container-fluid py-4">
    <!-- NAVIGATION BACK BUTTON -->
    <div class="mb-3">
        <a href="{{ route('academy.camps.index') }}" class="btn btn-sm btn-light border fw-bold text-dark shadow-sm px-3 py-2 rounded-3">
            <i class="fa-solid fa-arrow-right me-1 text-primary"></i>
            {{ $isArabic ? 'الرجوع إلى قائمة المعسكرات' : 'Back to Camps List' }}
        </a>
    </div>

    <!-- CAMP TOP HEADER BANNER -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge {{ $camp->type === 'international' ? 'bg-warning text-dark' : 'bg-info text-white' }} fw-bold px-3 py-2">
                            @if($camp->type === 'international')
                                <i class="fa-solid fa-plane me-1"></i> {{ $isArabic ? 'معسكر دولي (خارج مصر)' : 'International Camp' }}
                            @else
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $isArabic ? 'معسكر محلي (داخل مصر)' : 'Domestic Camp' }}
                            @endif
                        </span>
                        <span class="badge bg-white bg-opacity-25 px-3 py-2 text-white">{{ $camp->starts_on?->format('d M Y') }} - {{ $camp->ends_on?->format('d M Y') }}</span>
                    </div>
                    <h2 class="fw-bold mb-1">{{ $camp->title }}</h2>
                    <p class="mb-0 text-white-50">
                        <i class="fa-solid fa-hotel me-1"></i> {{ $camp->hotel_name ?: ($isArabic ? 'غير محدد' : 'N/A') }}
                        <span class="mx-2">|</span>
                        <i class="fa-solid fa-trophy me-1"></i> {{ $camp->sport?->name ?: ($isArabic ? 'متعدد الرياضات' : 'Multi-Sport') }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('academy.camps.edit', $camp->id) }}" class="btn btn-warning fw-bold text-dark shadow-sm">
                        <i class="fa-solid fa-pen-to-square me-1"></i>
                        {{ $isArabic ? 'تعديل بيانات المعسكر' : 'Edit Camp' }}
                    </a>
                    <a href="{{ route('academy.camps.export-roster', $camp->id) }}" class="btn btn-light fw-bold text-primary shadow-sm">
                        <i class="fa-solid fa-file-excel me-1 text-success"></i>
                        {{ $isArabic ? 'تصدير الكشف (CSV)' : 'Export Roster' }}
                    </a>
                    <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                        <i class="fa-solid fa-user-plus me-1"></i>
                        {{ $isArabic ? 'تسجيل مشترك جديد' : 'Add Camper' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUMMARY METRICS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <span class="text-muted small d-block">{{ $isArabic ? 'إجمالي الاشتراكات المحصلة' : 'Collected Revenue' }}</span>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($totalRevenue, 2) }} <small class="fs-6">{{ $currency['symbol'] }}</small></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <span class="text-muted small d-block">{{ $isArabic ? 'إجمالي مصروفات المعسكر' : 'Camp Expenses' }}</span>
                <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($totalExpenses, 2) }} <small class="fs-6">{{ $currency['symbol'] }}</small></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <span class="text-muted small d-block">{{ $isArabic ? 'صافي أرباح المعسكر' : 'Net Camp Profit' }}</span>
                <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }} mb-0 mt-1">{{ number_format($netProfit, 2) }} <small class="fs-6">{{ $currency['symbol'] }}</small></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <span class="text-muted small d-block">{{ $isArabic ? 'نسبة الإشغال (المشاركين)' : 'Occupancy' }}</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $camp->participants->count() }} / {{ $camp->capacity }} <small class="fs-6 text-muted">({{ $occupancyPct }}%)</small></h3>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom p-2">
            <ul class="nav nav-pills nav-fill gap-2" id="campTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link active fw-bold py-2" id="tab-participants-btn" data-bs-toggle="tab" data-bs-target="#tab-participants" type="button">
                        <i class="fa-solid fa-users me-1"></i> {{ $isArabic ? 'سجل المشتركين والمسافرين (' . $camp->participants->count() . ')' : 'Participants & Roster' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link fw-bold py-2" id="tab-financials-btn" data-bs-toggle="tab" data-bs-target="#tab-financials" type="button">
                        <i class="fa-solid fa-coins me-1"></i> {{ $isArabic ? 'المصروفات والربحية' : 'Financials & Expenses' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link fw-bold py-2" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button">
                        <i class="fa-solid fa-circle-info me-1"></i> {{ $isArabic ? 'تفاصيل المعسكر والخدمات' : 'Overview & Services' }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="campTabsContent">
                <!-- TAB 1: PARTICIPANTS -->
                <div class="tab-pane fade show active" id="tab-participants" role="tabpanel">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i> {{ $isArabic ? 'قائمة الطلاب المسجلين بالمعسكر' : 'Camp Registered Participants' }}</h5>
                        <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                            <i class="fa-solid fa-plus me-1"></i> {{ $isArabic ? 'إضافة مشترك' : 'Add Camper' }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ $isArabic ? 'اسم المشارك' : 'Name' }}</th>
                                    <th>{{ $isArabic ? 'الهاتف' : 'Phone' }}</th>
                                    <th>{{ $isArabic ? 'الجواز / التأشيرة' : 'Passport / Visa' }}</th>
                                    <th>{{ $isArabic ? 'الغرفة / المقاس' : 'Room / Size' }}</th>
                                    <th>{{ $isArabic ? 'الرسوم' : 'Total Fee' }}</th>
                                    <th>{{ $isArabic ? 'المدفوع' : 'Paid' }}</th>
                                    <th>{{ $isArabic ? 'تذكير واتساب' : 'WhatsApp' }}</th>
                                    <th>{{ $isArabic ? 'الإجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($camp->participants as $index => $p)
                                    @php
                                        $phoneClean = preg_replace('/[^0-9]/', '', $p->phone);
                                        $waMsg = rawurlencode($isArabic 
                                            ? "مرحباً {$p->name}، نود تذكيرك بتفاصيل ومواعيد معسكر {$camp->title}. إجمالي الرسوم: {$p->total_fee} {$currency['symbol']}، المدفوع: {$p->paid_amount} {$currency['symbol']}."
                                            : "Hello {$p->name}, details for camp {$camp->title}. Total: {$p->total_fee}, Paid: {$p->paid_amount}.");
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark d-block">{{ $p->name }}</strong>
                                            @if($p->student)
                                                <small class="badge bg-light text-primary border">{{ $isArabic ? 'طالب بالأكاديمية' : 'Academy Student' }}</small>
                                            @endif
                                        </td>
                                        <td><span dir="ltr">{{ $p->phone }}</span></td>
                                        <td>
                                            @if($p->passport_number)
                                                <small class="d-block text-dark">🛂 {{ $p->passport_number }}</small>
                                            @endif
                                            <span class="badge {{ $p->visa_status === 'issued' ? 'bg-success' : ($p->visa_status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ $p->visa_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="d-block text-muted">{{ $isArabic ? 'غرفة:' : 'Room:' }} {{ $p->room_number ?: '-' }}</small>
                                            <small class="d-block text-muted">{{ $isArabic ? 'مقاس:' : 'Size:' }} {{ $p->tshirt_size ?: '-' }}</small>
                                        </td>
                                        <td><strong>{{ number_format($p->total_fee, 0) }}</strong></td>
                                        <td>
                                            <span class="text-success fw-bold">{{ number_format($p->paid_amount, 0) }}</span>
                                            @if($p->remaining_fee > 0)
                                                <small class="d-block text-danger">{{ $isArabic ? 'متبقي:' : 'Rem:' }} {{ number_format($p->remaining_fee, 0) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($phoneClean)
                                                <a href="https://wa.me/{{ $phoneClean }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-success py-1 px-2 text-white fw-bold">
                                                    <i class="fa-brands fa-whatsapp me-1"></i> {{ $isArabic ? 'تذكير' : 'Remind' }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('academy.camps.participants.destroy', [$camp->id, $p->id]) }}" onsubmit="return confirm('{{ $isArabic ? 'هل أنت تأكد من حذف المشترك؟' : 'Remove participant?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">{{ $isArabic ? 'لم يتم تسجيل أي مشاركين بعد' : 'No participants registered yet' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: FINANCIALS & EXPENSES -->
                <div class="tab-pane fade" id="tab-financials" role="tabpanel">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-coins text-danger me-2"></i> {{ $isArabic ? 'سجل مصروفات المعسكر' : 'Camp Expenses Log' }}</h5>
                        <button class="btn btn-sm btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="fa-solid fa-plus me-1"></i> {{ $isArabic ? 'تسجيل مصروف معسكر' : 'Add Expense' }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ $isArabic ? 'بيان المصروف' : 'Title' }}</th>
                                    <th>{{ $isArabic ? 'التصنيف' : 'Category' }}</th>
                                    <th>{{ $isArabic ? 'التاريخ' : 'Date' }}</th>
                                    <th>{{ $isArabic ? 'المبلغ' : 'Amount' }}</th>
                                    <th>{{ $isArabic ? 'الإجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($camp->expenses as $index => $exp)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $exp->title }}</strong></td>
                                        <td><span class="badge bg-light text-dark border">{{ $exp->category?->name_ar ?: ($isArabic ? 'عام' : 'General') }}</span></td>
                                        <td>{{ $exp->expense_date?->format('Y-m-d') }}</td>
                                        <td><strong class="text-danger">{{ number_format($exp->amount, 2) }} {{ $currency['symbol'] }}</strong></td>
                                        <td>
                                            <form method="POST" action="{{ route('academy.camps.expenses.destroy', [$camp->id, $exp->id]) }}" onsubmit="return confirm('{{ $isArabic ? 'حذف المصروف؟' : 'Delete expense?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">{{ $isArabic ? 'لم يتم تسجيل مصروفات لهذا المعسكر بعد' : 'No expenses recorded for this camp yet' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: OVERVIEW -->
                <div class="tab-pane fade" id="tab-overview" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <h6 class="fw-bold text-dark mb-2">{{ $isArabic ? 'تفاصيل الموقع والإقامة' : 'Location & Hotel' }}</h6>
                                <p class="mb-1"><strong>{{ $isArabic ? 'البلد:' : 'Country:' }}</strong> {{ $camp->country?->name ?: ($isArabic ? 'جمهورية مصر العربية' : 'Egypt') }}</p>
                                <p class="mb-1"><strong>{{ $isArabic ? 'المدينة:' : 'City:' }}</strong> {{ $camp->city_name ?: '-' }}</p>
                                <p class="mb-1"><strong>{{ $isArabic ? 'الفندق:' : 'Hotel:' }}</strong> {{ $camp->hotel_name ?: '-' }}</p>
                                <p class="mb-0"><strong>{{ $isArabic ? 'الملعب/النادي:' : 'Venue:' }}</strong> {{ $camp->venue_name ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <h6 class="fw-bold text-dark mb-2">{{ $isArabic ? 'الخدمات والتجهيزات المشمولة' : 'Included Services' }}</h6>
                                @if(!empty($camp->included_services))
                                    <ul class="mb-0 ps-3">
                                        @foreach($camp->included_services as $srv)
                                            <li>{{ ucfirst($srv) }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">{{ $isArabic ? 'لم تتم إضافة قائمة خدمات' : 'No services listed' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 1: ADD PARTICIPANT -->
<div class="modal fade" id="addParticipantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('academy.camps.participants.store', $camp->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> {{ $isArabic ? 'تسجيل مشترك جديد بالمعسكر' : 'Register New Camper' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'اسم المشترك' : 'Participant Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="{{ $isArabic ? 'الاسم الثلاثي' : 'Full Name' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'رقم الهاتف' : 'Phone' }} <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'هاتف الطوارئ / ولي الأمر' : 'Emergency Phone' }}</label>
                            <input type="text" name="emergency_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'رقم الجواز (للمعسكرات الدولية)' : 'Passport Number' }}</label>
                            <input type="text" name="passport_number" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ انتهاء الجواز' : 'Passport Expiry' }}</label>
                            <input type="date" name="passport_expiry" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'حالة التأشيرة (Visa)' : 'Visa Status' }}</label>
                            <select name="visa_status" class="form-select">
                                <option value="not_required">{{ $isArabic ? 'غير مطلوبة' : 'Not Required' }}</option>
                                <option value="pending">{{ $isArabic ? 'قيد الإجراء' : 'Pending' }}</option>
                                <option value="issued">{{ $isArabic ? 'تم الإصدار' : 'Issued' }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'مقاس الزي (T-Shirt)' : 'T-Shirt Size' }}</label>
                            <select name="tshirt_size" class="form-select">
                                <option value="S">S</option>
                                <option value="M" selected>M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'رقم الغرفة' : 'Room Number' }}</label>
                            <input type="text" name="room_number" class="form-control" placeholder="101">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'إجمالي رسوم الاشتراك' : 'Total Fee' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="total_fee" class="form-control" value="{{ $camp->price }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $isArabic ? 'المبلغ المدفوع الآن' : 'Paid Amount' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="{{ $camp->deposit_amount }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'حالة التسجيل' : 'Registration Status' }}</label>
                            <select name="status" class="form-select">
                                <option value="confirmed" selected>{{ $isArabic ? 'مؤكد' : 'Confirmed' }}</option>
                                <option value="registered">{{ $isArabic ? 'مسجل مبدئياً' : 'Registered' }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $isArabic ? 'ملاحظات طبية / خاصة' : 'Medical / Special Notes' }}</label>
                            <input type="text" name="medical_notes" class="form-control" placeholder="{{ $isArabic ? 'حساسية، علاج خاص...' : 'Allergies, etc.' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $isArabic ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary fw-bold">{{ $isArabic ? 'حفظ وحجز المقعد' : 'Save Participant' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: ADD EXPENSE -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('academy.camps.expenses.store', $camp->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-plus-circle me-2"></i> {{ $isArabic ? 'تسجيل مصروف جديد للمعسكر' : 'Record Camp Expense' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $isArabic ? 'عنوان المصروف' : 'Expense Title' }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="{{ $isArabic ? 'مثال: حجز ملاعب خارجية / اتوبيسات' : 'e.g., Bus Transport / Stadium Hire' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $isArabic ? 'التصنيف' : 'Category' }}</label>
                        <select name="category_id" class="form-select">
                            <option value="">{{ $isArabic ? 'مصروف عام' : 'General Expense' }}</option>
                            @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $isArabic ? 'المبلغ (' . $currency['symbol'] . ')' : 'Amount' }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $isArabic ? 'تاريخ المصروف' : 'Expense Date' }} <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $isArabic ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-danger fw-bold">{{ $isArabic ? 'حفظ المصروف' : 'Save Expense' }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
