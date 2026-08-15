<div class="d-inline-flex gap-1 align-items-center">
    <a href="{{ route('academy.coach.edit', $coach) }}" class="btn btn-sm btn-outline-primary" title="{{ trans('admin.edit') }}">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>

    @if($coach->phone)
        @php
            $cPhone = preg_replace('/\D+/', '', (string) $coach->phone);
            if ($cPhone && str_starts_with($cPhone, '0')) $cPhone = '2' . $cPhone;
        @endphp
        <a href="https://api.whatsapp.com/send?phone={{ $cPhone }}" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    @endif

    <form action="{{ route('academy.coach.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ trans('admin.coaches.delete_confirm') ?: (app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذا المدرب؟' : 'Are you sure you want to delete this coach?') }}')">
        @csrf
        <input type="hidden" name="id" value="{{ $coach->id }}">
        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ trans('admin.delete') }}">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
</div>
