<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('admin.student_management.students_print') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; letter-spacing: 0; }
        body { margin: 0; background: #eef2f6; color: #182230; font-family: Cairo, Arial, sans-serif; }
        .print-actions { max-width: 1120px; margin: 18px auto 0; display: flex; justify-content: flex-end; }
        .print-actions button { min-height: 42px; border: 0; border-radius: 8px; padding: 0 18px; background: #176b87; color: #fff; font-weight: 800; cursor: pointer; }
        .sheet { max-width: 1120px; margin: 20px auto; background: #fff; padding: 28px; border: 1px solid #d0d5dd; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding-bottom: 18px; border-bottom: 2px solid #182230; }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand img { width: 74px; height: 74px; object-fit: contain; border: 1px solid #e4e7ec; border-radius: 8px; padding: 8px; }
        h1, h2, p { margin: 0; }
        h1 { font-size: 26px; font-weight: 900; }
        .muted { color: #667085; }
        .meta { text-align: end; font-size: 13px; color: #475467; }
        .title-row { display: flex; justify-content: space-between; gap: 12px; margin: 24px 0 12px; }
        .title-row h2 { font-size: 22px; font-weight: 900; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d0d5dd; padding: 8px; text-align: inherit; vertical-align: middle; font-size: 12px; }
        th { background: #f2f4f7; font-weight: 900; }
        .avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 1px solid #d0d5dd; }
        footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #d0d5dd; display: flex; justify-content: space-between; color: #475467; font-size: 12px; }
        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .sheet { margin: 0; max-width: none; border: 0; padding: 12mm; }
            th, td { font-size: 10px; padding: 6px; }
        }
        @page { size: A4 landscape; margin: 8mm; }
    </style>
</head>
<body>
    <div class="print-actions">
        <button onclick="window.print()">{{ trans('admin.student_management.print') }}</button>
    </div>

    <main class="sheet">
        <header>
            <div class="brand">
                <img src="{{ $academy?->logo }}" alt="Academy logo">
                <div>
                    <h1>{{ $academy?->commercial_name ?: 'Academy' }}</h1>
                    <p class="muted">{{ trans('admin.student_management.students_print') }}</p>
                </div>
            </div>
            <div class="meta">
                <p>{{ trans('admin.student_management.date') }}: {{ now()->format('Y-m-d H:i') }}</p>
                <p>{{ trans('admin.student_management.students') }}: {{ $students->count() }}</p>
            </div>
        </header>

        <div class="title-row">
            <h2>{{ trans('admin.student_management.students_list_title') }}</h2>
            <p class="muted">{{ trans('admin.student_management.official_sheet') }}</p>
        </div>

        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('admin.banners.image') }}</th>
                <th>{{ trans('admin.student_management.name') }}</th>
                <th>{{ trans('admin.student_management.phone') }}</th>
                <th>{{ trans('admin.student_management.email') }}</th>
                <th>{{ trans('admin.student_management.gender') }}</th>
                <th>{{ trans('admin.student_management.birth_date') }}</th>
                <th>{{ trans('admin.student_management.guardian') }}</th>
                <th>{{ trans('admin.student_management.status') }}</th>
                <th>{{ trans('admin.student_management.medical_notes') }}</th>
                <th>{{ trans('admin.student_management.notes') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><img src="{{ $student->avatarUrl() }}" class="avatar" alt="{{ $student->name }}" onerror="this.onerror=null;this.src='{{ $student->defaultImageUrl() }}';"></td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->phone ?: '-' }}</td>
                    <td>{{ $student->email ?: '-' }}</td>
                    <td>{{ $student->gender ? trans('admin.student_management.' . $student->gender) : '-' }}</td>
                    <td>{{ $student->birth_date?->format('Y-m-d') ?: '-' }}</td>
                    <td>{{ $student->guardian_name ?: '-' }}<br>{{ $student->guardian_phone ?: '' }}</td>
                    <td>{{ trans('admin.student_management.' . $student->status) }}</td>
                    <td>{{ $student->medical_notes ?: '-' }}</td>
                    <td>{{ $student->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="11">{{ trans('admin.student_management.no_students_yet') }}</td></tr>
            @endforelse
            </tbody>
        </table>

        <footer>
            <span>{{ app()->getLocale() === 'ar' ? 'تمت الطباعة من منظومة حجز الرقمية' : 'Printed from Hagzz digital platform' }}</span>
            <strong>{{ app()->getLocale() === 'ar' ? 'من شركة ميسك القطرية' : 'By Mesk Qatar Company' }}</strong>
        </footer>
    </main>
</body>
</html>
