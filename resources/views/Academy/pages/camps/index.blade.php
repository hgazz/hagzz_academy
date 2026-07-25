@extends('Academy.Layouts.master')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('title', $isArabic ? 'إدارة المعسكرات التدريبية' : 'Training Camps Management')

@section('content')
<div class="container-fluid py-4">
    <!-- TOP PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-plane-departure text-primary me-2"></i>
                {{ $isArabic ? 'إدارة المعسكرات التدريبية' : 'Training Camps Management' }}
            </h3>
            <p class="text-muted small mb-0">
                {{ $isArabic ? 'تنظيم وإدارة المعسكرات الداخلية (مصر) والدولية (خارج مصر) لجميع الرياضات ومتابعة المشتركين والأرباح.' : 'Organize domestic & international camps across all sports and track participants & net profits.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('academy.camps.create') }}" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i>
                {{ $isArabic ? 'إطلاق معسكر جديد' : 'Launch New Camp' }}
            </a>
        </div>
    </div>

    <!-- STATS STRIP -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                        <i class="fa-solid fa-campground fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">{{ $isArabic ? 'إجمالي المعسكرات' : 'Total Camps' }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalCamps) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                        <i class="fa-solid fa-flag-checkered fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">{{ $isArabic ? 'معسكرات داخل مصر' : 'Domestic Camps' }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($domesticCamps) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                        <i class="fa-solid fa-earth-americas fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">{{ $isArabic ? 'معسكرات دولية' : 'International Camps' }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($internationalCamps) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">{{ $isArabic ? 'إجمالي المشاركين' : 'Total Participants' }}</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalParticipants) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('academy.camps.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ $isArabic ? 'بحث باسم المعسكر، الفندق...' : 'Search camp name, hotel...' }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">{{ $isArabic ? 'جميع أنواع المعسكرات' : 'All Camp Types' }}</option>
                        <option value="domestic" {{ request('type') === 'domestic' ? 'selected' : '' }}>{{ $isArabic ? '🇪🇬 معسكرات داخلية (مصر)' : 'Domestic Camps' }}</option>
                        <option value="international" {{ request('type') === 'international' ? 'selected' : '' }}>{{ $isArabic ? '✈️ معسكرات دولية (خارج مصر)' : 'International Camps' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sport_id" class="form-select">
                        <option value="">{{ $isArabic ? 'جميع الرياضات' : 'All Sports' }}</option>
                        @foreach($sports as $s)
                            <option value="{{ $s->id }}" {{ request('sport_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ $isArabic ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>{{ $isArabic ? 'قادم' : 'Upcoming' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ $isArabic ? 'جاري الآن' : 'Active' }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ $isArabic ? 'مكتمل' : 'Completed' }}</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-filter me-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CAMPS GRID -->
    <div class="row g-4">
        @forelse($camps as $camp)
            @php
                $occupancyPct = $camp->capacity > 0 ? min(100, round(($camp->participants_count / $camp->capacity) * 100)) : 0;
                $statusClass = match($camp->status) {
                    'active' => 'bg-success text-white',
                    'upcoming' => 'bg-primary text-white',
                    'completed' => 'bg-secondary text-white',
                    'cancelled' => 'bg-danger text-white',
                    default => 'bg-light text-dark'
                };
                $statusText = match($camp->status) {
                    'active' => ($isArabic ? 'جاري الآن' : 'Active'),
                    'upcoming' => ($isArabic ? 'قادم' : 'Upcoming'),
                    'completed' => ($isArabic ? 'مكتمل' : 'Completed'),
                    'cancelled' => ($isArabic ? 'ملغى' : 'Cancelled'),
                    default => ($isArabic ? 'مسودة' : 'Draft')
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                        <span class="badge {{ $camp->type === 'international' ? 'bg-purple text-white bg-opacity-75' : 'bg-info text-white' }} px-3 py-2 fw-bold">
                            @if($camp->type === 'international')
                                <i class="fa-solid fa-plane me-1"></i> {{ $isArabic ? 'معسكر دولي' : 'International' }}
                            @else
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $isArabic ? 'معسكر محلي (مصر)' : 'Domestic' }}
                            @endif
                        </span>
                        <span class="badge {{ $statusClass }} px-2 py-1">{{ $statusText }}</span>
                    </div>

                    <div class="card-body p-3">
                        <h5 class="fw-bold text-dark mb-2">{{ $camp->title }}</h5>
                        <p class="text-muted small mb-3 text-truncate" title="{{ $camp->description }}">
                            {{ $camp->description ?: ($isArabic ? 'لا يوجد وصف تفصيلي' : 'No description') }}
                        </p>

                        <div class="bg-light rounded p-2 mb-3 small">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted"><i class="fa-solid fa-calendar me-1 text-primary"></i> {{ $isArabic ? 'الفترة:' : 'Dates:' }}</span>
                                <strong class="text-dark">{{ $camp->starts_on?->format('d M') }} - {{ $camp->ends_on?->format('d M Y') }}</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted"><i class="fa-solid fa-hotel me-1 text-primary"></i> {{ $isArabic ? 'الإقامة:' : 'Hotel:' }}</span>
                                <strong class="text-dark">{{ $camp->hotel_name ?: ($camp->country?->name ?: ($isArabic ? 'غير محدد' : 'N/A')) }}</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted"><i class="fa-solid fa-coins me-1 text-primary"></i> {{ $isArabic ? 'سعر الفرد:' : 'Price/Person:' }}</span>
                                <strong class="text-success">{{ number_format($camp->price, 0) }} {{ $currency['symbol'] }}</strong>
                            </div>
                        </div>

                        <!-- Occupancy Progress -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>{{ $isArabic ? 'المقاعد الحالية:' : 'Occupancy:' }} {{ $camp->participants_count }} / {{ $camp->capacity }}</span>
                                <span>{{ $occupancyPct }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $occupancyPct >= 90 ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ $occupancyPct }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex gap-1">
                            <a href="{{ route('academy.camps.show', $camp->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                <i class="fa-solid fa-eye me-1"></i> {{ $isArabic ? 'عرض التفاصيل والطلاب' : 'View Hub' }}
                            </a>
                            <a href="{{ route('academy.camps.edit', $camp->id) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold" title="{{ $isArabic ? 'تعديل المعسكر' : 'Edit' }}">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </div>
                        <form method="POST" action="{{ route('academy.camps.destroy', $camp->id) }}" onsubmit="return confirm('{{ $isArabic ? 'هل أنت تأكد من حذف هذا المعسكر؟' : 'Are you sure you want to delete this camp?' }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-3 shadow-sm">
                    <i class="fa-solid fa-campground fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">{{ $isArabic ? 'لا توجد معسكرات مسجلة حالياً' : 'No Training Camps Found' }}</h5>
                    <p class="text-muted small mb-3">{{ $isArabic ? 'قم بإطلاق معسكرك التدريبي الأول سواء داخل مصر أو خارج مصر بكل سهولة.' : 'Launch your first domestic or international training camp easily.' }}</p>
                    <a href="{{ route('academy.camps.create') }}" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-plus-circle me-1"></i> {{ $isArabic ? 'إضافة معسكر جديد' : 'Add New Camp' }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $camps->links() }}
    </div>
</div>
@endsection
