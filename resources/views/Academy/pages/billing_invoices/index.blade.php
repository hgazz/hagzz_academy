@extends('Academy.Layouts.master')
@section('title', app()->getLocale()==='ar' ? 'فواتير اشتراك Hagzz' : 'Hagzz subscription invoices')
@section('content')
@php($ar=app()->getLocale()==='ar')
<div class="middle-content container-xxl p-0">
 <div class="mb-4"><h3>{{ $ar?'فواتير اشتراك منصة Hagzz':'Hagzz platform subscription invoices' }}</h3><p class="text-muted">{{ $ar?'راجع المبالغ المستحقة والمدفوعة واطبع أي فاتورة بالمقاس المناسب.':'Review balances and print any invoice in the required paper format.' }}</p></div>
 <div class="table-responsive bg-white rounded shadow-sm"><table class="table table-hover align-middle mb-0"><thead><tr><th>{{ $ar?'رقم الفاتورة':'Invoice' }}</th><th>{{ $ar?'الخطة والفترة':'Plan & period' }}</th><th>{{ $ar?'الإجمالي':'Total' }}</th><th>{{ $ar?'المدفوع':'Paid' }}</th><th>{{ $ar?'المتبقي':'Balance' }}</th><th>{{ $ar?'الحالة':'Status' }}</th><th>{{ $ar?'طباعة':'Print' }}</th></tr></thead><tbody>
 @forelse($invoices as $invoice)<tr><td class="fw-bold">{{ $invoice->invoice_number }}</td><td>{{ $invoice->subscription?->plan?->name ?: '-' }}<small class="d-block text-muted">{{ $invoice->period_starts_at?->format('Y-m-d') }} — {{ $invoice->period_ends_at?->format('Y-m-d') }}</small></td><td>{{ number_format($invoice->total_amount,2) }} {{ $invoice->currency_code }}</td><td class="text-success">{{ number_format($invoice->paid_amount,2) }}</td><td class="{{ $invoice->balance>0?'text-danger':'' }}">{{ number_format($invoice->balance,2) }}</td><td><span class="badge bg-{{ $invoice->status==='paid'?'success':($invoice->status==='void'?'secondary':'warning') }}">{{ $invoice->status }}</span></td><td><div class="btn-group">@foreach(['a4'=>'A4','a5'=>'A5','pos'=>'POS'] as $paper=>$label)<a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('academy.invoices.platform.print',['invoice'=>$invoice,'paper'=>$paper]) }}">{{ $label }}</a>@endforeach</div></td></tr>
 @empty<tr><td colspan="7" class="text-center py-5 text-muted">{{ $ar?'لا توجد فواتير صادرة حتى الآن.':'No invoices have been issued yet.' }}</td></tr>@endforelse
 </tbody></table></div><div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
