@extends('Academy.Layouts.master')

@section('title', app()->getLocale() === 'ar' ? 'إدارة المصروفات وصافي الربح' : 'Expenses & Net Profit Management')

@section('content')
@php($ar = app()->getLocale() === 'ar')

<div class="middle-content container-xxl p-0">
    <!-- BREADCRUMBS -->
    <div class="secondary-nav mb-4">
        <div class="breadcrumbs-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="btn-toggle sidebarCollapse"><i data-feather="menu"></i></a>
                <div class="d-flex breadcrumb-content">
                    <nav class="breadcrumb-style-one">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ $ar ? 'إدارة المصروفات وصافي الربح' : 'Expenses & Net Profit' }}</li>
                        </ol>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <!-- SUMMARY METRIC CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold uppercase">{{ $ar ? 'إجمالي الإيرادات (الفترة)' : 'Total Revenue (Period)' }}</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($totalRevenue, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small></h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                        <i class="fa-solid fa-hand-holding-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold uppercase">{{ $ar ? 'إجمالي المصروفات (بالعملة المحلية)' : 'Total Expenses (Base Currency)' }}</span>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($totalExpenses, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small></h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                        <i class="fa-solid fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start {{ $netProfit >= 0 ? 'border-primary' : 'border-warning' }} border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold uppercase">{{ $ar ? 'صافي الربح الفعلي (الإيراد - المصروف)' : 'Net Profit (Revenue - Expense)' }}</span>
                        <h3 class="fw-bold {{ $netProfit >= 0 ? 'text-primary' : 'text-warning' }} mb-0 mt-1">{{ number_format($netProfit, 2) }} <small class="fs-6">{{ $academyCurrencySymbol }}</small></h3>
                    </div>
                    <div class="rounded-circle {{ $netProfit >= 0 ? 'bg-primary text-primary' : 'bg-warning text-warning' }} bg-opacity-10 p-3">
                        <i class="fa-solid fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS & FILTERS CARD -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-filter me-2 text-primary"></i> {{ $ar ? 'تصفية المصروفات' : 'Filter Expenses' }}</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#newCategoryModal">
                        <i class="fa-solid fa-folder-plus me-1"></i> {{ $ar ? 'تصنيف مصروف جديد' : 'New Category' }}
                    </button>
                    <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="fa-solid fa-plus me-1"></i> {{ $ar ? 'تسجيل مصروف جديد' : 'Record Expense' }}
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('academy.expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ $ar ? 'دورة المصروف' : 'Period Type' }}</label>
                    <select name="period_type" class="form-select">
                        <option value="">{{ $ar ? 'جميع الفترات' : 'All Periods' }}</option>
                        <option value="daily" @selected(request('period_type') == 'daily')>{{ $ar ? 'يومي (Daily)' : 'Daily' }}</option>
                        <option value="monthly" @selected(request('period_type') == 'monthly')>{{ $ar ? 'شهري (Monthly)' : 'Monthly' }}</option>
                        <option value="quarterly" @selected(request('period_type') == 'quarterly')>{{ $ar ? 'ربع سنوي (Quarterly)' : 'Quarterly' }}</option>
                        <option value="annual" @selected(request('period_type') == 'annual')>{{ $ar ? 'سنوي (Annual)' : 'Annual' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">{{ $ar ? 'تصنيف المصروف' : 'Category' }}</label>
                    <select name="category_id" class="form-select">
                        <option value="">{{ $ar ? 'جميع التصنيفات' : 'All Categories' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">{{ $ar ? 'من تاريخ' : 'From Date' }}</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">{{ $ar ? 'إلى تاريخ' : 'To Date' }}</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100 fw-bold"><i class="fa-solid fa-magnifying-glass me-1"></i> {{ $ar ? 'تصفية' : 'Filter' }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EXPENSES TABLE CARD -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title m-0 fw-bold"><i class="fa-solid fa-list-check text-primary me-2"></i> {{ $ar ? 'سجل المصروفات المسجلة' : 'Recorded Expenses List' }}</h5>
            <span class="badge bg-secondary fs-6">{{ $expenses->total() }} {{ $ar ? 'مصروف' : 'record' }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ $ar ? 'عنوان المصروف' : 'Expense Title' }}</th>
                            <th>{{ $ar ? 'التصنيف' : 'Category' }}</th>
                            <th>{{ $ar ? 'المبلغ المسدد' : 'Paid Amount' }}</th>
                            <th>{{ $ar ? 'المقابل بعملة المنشأة' : 'Base Amount' }}</th>
                            <th>{{ $ar ? 'نوع الدورة' : 'Period Type' }}</th>
                            <th>{{ $ar ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ $ar ? 'المعتمد' : 'Approved By' }}</th>
                            <th>{{ $ar ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td>{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                                <td class="fw-bold">
                                    <i class="fa-solid {{ $exp->category?->icon ?: 'fa-receipt' }} text-primary me-1"></i>
                                    {{ $exp->title }}
                                </td>
                                <td><span class="badge bg-light text-dark border"><i class="fa-solid fa-tag me-1"></i> {{ $exp->category?->name ?: '-' }}</span></td>
                                <td class="fw-bold text-dark fs-6">{{ number_format($exp->amount, 2) }} <small class="text-muted">{{ $exp->currency }}</small></td>
                                <td class="fw-bold text-danger fs-6">
                                    {{ number_format($exp->base_amount ?: $exp->amount, 2) }} <small>{{ $exp->base_currency ?: $academyCurrencyCode }}</small>
                                    @if($exp->currency !== ($exp->base_currency ?: $academyCurrencyCode))
                                        <div class="small text-muted font-monospace opacity-75">(1 {{ $exp->currency }} = {{ number_format($exp->exchange_rate, 2) }})</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-info text-dark">{{ ucfirst($exp->period_type) }}</span></td>
                                <td>{{ $exp->expense_date?->format('Y-m-d') }}</td>
                                <td><i class="fa-solid fa-user-check text-success me-1"></i> {{ $exp->approved_by ?: '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($exp->receipt_image)
                                            <a href="{{ asset('storage/' . $exp->receipt_image) }}" target="_blank" class="btn btn-sm btn-outline-info" title="{{ $ar ? 'معاينة الإيصال' : 'View Receipt' }}">
                                                <i class="fa-solid fa-paperclip"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('academy.expenses.destroy', $exp->id) }}" onsubmit="return confirm('{{ $ar ? 'هل أنت تأكد من حذف هذا المصروف؟' : 'Are you sure you want to delete this expense?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $ar ? 'حذف' : 'Delete' }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-receipt fa-2x mb-2 d-block text-muted opacity-50"></i>
                                    {{ $ar ? 'لا يوجد مصروفات مسجلة بهذه الفلاتر.' : 'No expenses recorded with these filters.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($expenses->hasPages())
                <div class="p-3">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL: ADD EXPENSE WITH MULTI-CURRENCY -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('academy.expenses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i> {{ $ar ? 'تسجيل مصروف جديد' : 'Record New Expense' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $ar ? 'عنوان/بيان المصروف' : 'Expense Title' }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="{{ $ar ? 'مثال: إيجار الملعب الرئيسي أو شراء ادوات بالدولار' : 'e.g. Main Pitch Rent or Equipment in USD' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $ar ? 'تصنيف المصروف' : 'Expense Category' }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">{{ $ar ? '-- اختر التصنيف --' : '-- Select Category --' }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- AMOUNT & CURRENCY SELECTOR -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $ar ? 'المبلغ المدفوع' : 'Amount Paid' }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" id="exp_amount" name="amount" class="form-control fw-bold" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $ar ? 'عملة السداد' : 'Payment Currency' }} <span class="text-danger">*</span></label>
                            <select id="exp_currency" name="currency" class="form-select fw-bold" required>
                                <option value="{{ $academyCurrencyCode }}" selected>{{ $academyCurrencyCode }} ({{ $academyCurrencySymbol }}) - {{ $ar ? 'عملة المنشأة الأساسية' : 'Main Currency' }}</option>
                                <option value="USD">USD ($) - {{ $ar ? 'دولار أمريكي' : 'US Dollar' }}</option>
                                <option value="EUR">EUR (€) - {{ $ar ? 'يورو أوروبي' : 'Euro' }}</option>
                                <option value="EGP">EGP (ج.م) - {{ $ar ? 'جنيه مصري' : 'Egyptian Pound' }}</option>
                                <option value="SAR">SAR (ر.س) - {{ $ar ? 'ريال سعودي' : 'Saudi Riyal' }}</option>
                                <option value="QAR">QAR (ر.ق) - {{ $ar ? 'ريال قطري' : 'Qatari Riyal' }}</option>
                                <option value="AED">AED (د.إ) - {{ $ar ? 'درهم إماراتي' : 'UAE Dirham' }}</option>
                                <option value="KWD">KWD (د.ك) - {{ $ar ? 'دينار كويتي' : 'Kuwaiti Dinar' }}</option>
                                <option value="BHD">BHD (د.ب) - {{ $ar ? 'دينار بحريني' : 'Bahraini Dinar' }}</option>
                                <option value="OMR">OMR (ر.ع) - {{ $ar ? 'ريال عماني' : 'Omani Rial' }}</option>
                            </select>
                        </div>
                        
                        <!-- FX EQUIVALENT FIELD (Shown when currency != base currency) -->
                        <div class="col-md-4 d-none" id="fx_wrapper">
                            <label class="form-label fw-bold text-primary">
                                <i class="fa-solid fa-calculator me-1"></i>
                                {{ $ar ? 'المقابل بـ '.$academyCurrencySymbol : 'Equivalent in '.$academyCurrencyCode }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="0.01" id="exp_base_amount" name="base_amount" class="form-control fw-bold border-primary" placeholder="0.00">
                            <span class="small text-muted d-block mt-1">{{ $ar ? 'لتحويل المصروف بدقة وعرض الأرباح الصحيحة' : 'For accurate P&L calculation' }}</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $ar ? 'تاريخ المصروف' : 'Expense Date' }} <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $ar ? 'دورة المصروف' : 'Period Type' }} <span class="text-danger">*</span></label>
                            <select name="period_type" class="form-select" required>
                                <option value="daily">{{ $ar ? 'يومي (Daily)' : 'Daily' }}</option>
                                <option value="monthly" selected>{{ $ar ? 'شهري (Monthly)' : 'Monthly' }}</option>
                                <option value="quarterly">{{ $ar ? 'ربع سنوي (Quarterly)' : 'Quarterly' }}</option>
                                <option value="annual">{{ $ar ? 'سنوي (Annual)' : 'Annual' }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ $ar ? 'الشخص المعتمد للمصروف' : 'Approved By' }}</label>
                            <input type="text" name="approved_by" class="form-control" value="{{ auth('academy')->user()?->name }}" placeholder="{{ $ar ? 'اسم المدير أو المعتمد' : 'Approver Name' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $ar ? 'صورة الإيصال / السند' : 'Receipt Image' }}</label>
                            <input type="file" name="receipt_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ $ar ? 'ملاحظات وتفاصيل إضافية' : 'Notes & Additional Info' }}</label>
                            <input type="text" name="notes" class="form-control" placeholder="{{ $ar ? 'تفاصيل أخرى عن المصروف...' : 'Additional notes...' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ $ar ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fa-solid fa-check-circle me-1"></i> {{ $ar ? 'حفظ المصروف' : 'Save Expense' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: ADD CATEGORY -->
<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('academy.expenses.categories.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-folder-plus text-primary me-2"></i> {{ $ar ? 'إضافة تصنيف مصروف جديد' : 'Add New Category' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $ar ? 'اسم التصنيف بالعربية' : 'Category Name (Arabic)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: رسوم تراخيص وتصاريح" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $ar ? 'اسم التصنيف بالإنجليزية' : 'Category Name (English)' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" placeholder="e.g. Licenses & Permits Fees" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ $ar ? 'إلغاء' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-primary fw-bold">{{ $ar ? 'حفظ التصنيف' : 'Save Category' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const currencySelect = document.getElementById('exp_currency');
    const fxWrapper = document.getElementById('fx_wrapper');
    const baseAmountInput = document.getElementById('exp_base_amount');
    const expAmountInput = document.getElementById('exp_amount');
    const baseCurrencyCode = "{{ $academyCurrencyCode }}";

    function toggleFX() {
        if (currencySelect.value !== baseCurrencyCode) {
            fxWrapper.classList.remove('d-none');
            baseAmountInput.setAttribute('required', 'required');
        } else {
            fxWrapper.classList.add('d-none');
            baseAmountInput.removeAttribute('required');
            baseAmountInput.value = expAmountInput.value;
        }
    }

    currencySelect.addEventListener('change', toggleFX);
    expAmountInput.addEventListener('input', function () {
        if (currencySelect.value === baseCurrencyCode) {
            baseAmountInput.value = expAmountInput.value;
        }
    });

    toggleFX();
});
</script>
@endsection
