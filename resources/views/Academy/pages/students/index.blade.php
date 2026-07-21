@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.students'))

@push('css')
    <style>
        .student-table td {
            vertical-align: middle;
        }

        .student-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            border: 2px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.18);
            background: #e8eef0;
        }

        .student-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
        }

        .student-import-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #e4e7ec;
            border-radius: 8px;
            background: #f8fafc;
        }

        .student-import-box input[type="file"] {
            max-width: 280px;
        }
    </style>
@endpush

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h3 class="mb-0">{{ trans('admin.student_management.students') }}</h3>
                        <div class="student-tools">
                            <a href="{{ route('academy.whatsapp.compose') }}" class="btn btn-success">
                                <i class="fa-brands fa-whatsapp"></i> {{ app()->getLocale() === 'ar' ? 'إرسال جماعي' : 'Bulk WhatsApp' }}
                            </a>
                            <a href="{{ route('academy.students.template') }}" class="btn btn-outline-secondary">
                                {{ trans('admin.student_management.download_students_template') }}
                            </a>
                            <a href="{{ route('academy.students.export') }}" class="btn btn-outline-success">
                                {{ trans('admin.student_management.export_excel') }}
                            </a>
                            <a href="{{ route('academy.students.print') }}" target="_blank" class="btn btn-outline-dark">
                                {{ trans('admin.student_management.print_pdf') }}
                            </a>
                            <a href="{{ route('academy.students.create') }}" class="btn btn-primary">{{ trans('admin.student_management.add_student') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academy.students.import') }}" method="POST" enctype="multipart/form-data" class="student-import-box">
                            @csrf
                            <strong>{{ trans('admin.student_management.import_students') }}</strong>
                            <input type="file" name="students_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <button class="btn btn-success">{{ trans('admin.student_management.upload_students_file') }}</button>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped student-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('admin.banners.image') }}</th>
                                    <th>{{ trans('admin.student_management.name') }}</th>
                                    <th>{{ trans('admin.student_management.phone') }}</th>
                                    <th>{{ trans('admin.student_management.guardian') }}</th>
                                    <th>{{ trans('admin.student_management.status') }}</th>
                                    <th>{{ trans('admin.student_management.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->id }}</td>
                                        <td>
                                            <img
                                                src="{{ $student->avatarUrl() }}"
                                                alt="{{ $student->name }}"
                                                class="student-avatar"
                                                loading="lazy"
                                                onerror="this.onerror=null;this.src='{{ $student->defaultImageUrl() }}';"
                                            >
                                        </td>
                                        <td><button type="button" class="student-profile-trigger" data-student-profile-url="{{ route('academy.students.profile', $student) }}">{{ $student->name }}</button></td>
                                        <td>{{ $student->phone ?? '-' }}</td>
                                        <td>{{ $student->guardian_name ?? '-' }}<br><small>{{ $student->guardian_phone }}</small></td>
                                        <td><span class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }}">{{ trans('admin.student_management.' . $student->status) }}</span></td>
                                        <td>
                                            @if($student->phone || $student->guardian_phone)
                                                <a href="{{ route('academy.whatsapp.compose', ['recipients' => ['student:'.$student->id]]) }}" class="btn btn-sm btn-success" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                            @endif
                                            <a href="{{ route('academy.students.card', $student) }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ app()->getLocale() === 'ar' ? 'الكارت' : 'Card' }}</a>
                                            <a href="{{ route('academy.students.edit', $student) }}" class="btn btn-sm btn-warning">{{ trans('admin.student_management.edit') }}</a>
                                            <form action="{{ route('academy.students.destroy', $student) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ trans('admin.student_management.delete_student_confirm') }}')">{{ trans('admin.student_management.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ trans('admin.student_management.no_students_yet') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Academy.pages.students._profile_modal')
@endsection
