<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('admin.student_management.competition_print') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; letter-spacing: 0; }
        body { margin: 0; background: #eef2f6; color: #182230; font-family: Cairo, Arial, sans-serif; }
        .print-wrap { max-width: 920px; margin: 24px auto; background: #fff; padding: 34px; border: 1px solid #d0d5dd; }
        .print-actions { max-width: 920px; margin: 18px auto 0; display: flex; gap: 10px; justify-content: flex-end; }
        .print-actions button { min-height: 42px; border: 0; border-radius: 8px; padding: 0 18px; background: #176b87; color: #fff; font-weight: 800; cursor: pointer; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 18px; border-bottom: 2px solid #182230; padding-bottom: 18px; }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand img { width: 74px; height: 74px; object-fit: contain; border: 1px solid #e4e7ec; border-radius: 8px; padding: 8px; }
        h1, h2, h3, p { margin: 0; }
        h1 { font-size: 28px; font-weight: 900; }
        .muted { color: #667085; }
        .meta { text-align: end; font-size: 13px; }
        .match { margin: 28px 0; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 18px; text-align: center; }
        .team { border: 1px solid #d0d5dd; border-radius: 8px; padding: 18px; }
        .team strong { display: block; font-size: 22px; margin-bottom: 10px; }
        .team span { display: block; font-size: 44px; font-weight: 900; color: #176b87; }
        .vs { font-size: 18px; font-weight: 900; color: #667085; }
        .details { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 24px; }
        .detail { border: 1px solid #e4e7ec; border-radius: 8px; padding: 12px; }
        .detail span { display: block; color: #667085; font-size: 12px; margin-bottom: 5px; }
        .detail strong { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d0d5dd; padding: 10px; text-align: inherit; }
        th { background: #f2f4f7; font-weight: 900; }
        .notes { margin-top: 24px; border: 1px solid #e4e7ec; border-radius: 8px; padding: 14px; min-height: 76px; }
        footer { margin-top: 34px; padding-top: 14px; border-top: 1px solid #d0d5dd; display: flex; justify-content: space-between; gap: 14px; color: #475467; font-size: 12px; }
        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .print-wrap { margin: 0; max-width: none; border: 0; padding: 18mm; }
        }
        @page { size: A4; margin: 10mm; }
    </style>
</head>
<body>
    <div class="print-actions">
        <button onclick="window.print()">{{ trans('admin.student_management.print') }}</button>
    </div>

    <main class="print-wrap">
        <header>
            <div class="brand">
                <img src="{{ $competition->academy?->logo }}" alt="Academy logo">
                <div>
                    <h1>{{ $competition->academy?->commercial_name ?: $competition->home_team_name }}</h1>
                    <p class="muted">{{ trans('admin.student_management.competition_print') }}</p>
                </div>
            </div>
            <div class="meta">
                <p>{{ trans('admin.student_management.date') }}: {{ now()->format('Y-m-d H:i') }}</p>
                <p>{{ trans('admin.student_management.status') }}: {{ trans('admin.student_management.' . $competition->status) }}</p>
            </div>
        </header>

        <section class="match">
            <div class="team"><strong>{{ $competition->home_team_name }}</strong><span>{{ $competition->home_score ?? '-' }}</span></div>
            <div class="vs">VS</div>
            <div class="team"><strong>{{ $competition->opponent_name }}</strong><span>{{ $competition->opponent_score ?? '-' }}</span></div>
        </section>

        <section class="details">
            <div class="detail"><span>{{ trans('admin.student_management.sport') }}</span><strong>{{ $competition->sport?->name ?: '-' }}</strong></div>
            <div class="detail"><span>{{ trans('admin.student_management.date') }}</span><strong>{{ $competition->competition_date?->format('Y-m-d') }}</strong></div>
            <div class="detail"><span>{{ trans('admin.student_management.time') }}</span><strong>{{ $competition->starts_at ?: '-' }}</strong></div>
            <div class="detail"><span>{{ trans('admin.student_management.venue') }}</span><strong>{{ $competition->venue ?: '-' }}</strong></div>
        </section>

        <h2>{{ trans('admin.student_management.nominated_players') }}</h2>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('admin.student_management.student') }}</th>
                <th>{{ trans('admin.student_management.phone') }}</th>
                <th>{{ trans('admin.student_management.guardian') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($competition->students as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->phone ?: '-' }}</td>
                    <td>{{ $student->guardian_name ?: '-' }} {{ $student->guardian_phone ? '(' . $student->guardian_phone . ')' : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">{{ app()->getLocale() === 'ar' ? 'لا توجد أسماء مسجلة.' : 'No players selected.' }}</td></tr>
            @endforelse
            </tbody>
        </table>

        <section class="notes">
            <h3>{{ trans('admin.student_management.notes') }}</h3>
            <p>{{ $competition->result_notes ?: ($competition->notes ?: '-') }}</p>
        </section>

        <footer>
            <span>{{ app()->getLocale() === 'ar' ? 'تمت الطباعة من منظومة حجز الرقمية' : 'Printed from Hagzz digital platform' }}</span>
            <strong>{{ app()->getLocale() === 'ar' ? 'من شركة ميسك القطرية' : 'By Mesk Qatar Company' }}</strong>
        </footer>
    </main>
</body>
</html>
