<table>
    <thead>
    <tr>
        <th colspan="11">{{ $academy?->commercial_name ?: 'Academy' }} - {{ trans('admin.student_management.students') }}</th>
    </tr>
    <tr>
        <th>#</th>
        <th>{{ trans('admin.banners.image') }}</th>
        <th>{{ trans('admin.student_management.name') }}</th>
        <th>{{ trans('admin.student_management.phone') }}</th>
        <th>{{ trans('admin.student_management.email') }}</th>
        <th>{{ trans('admin.student_management.gender') }}</th>
        <th>{{ trans('admin.student_management.birth_date') }}</th>
        <th>{{ trans('admin.student_management.guardian_name') }}</th>
        <th>{{ trans('admin.student_management.guardian_phone') }}</th>
        <th>{{ trans('admin.student_management.status') }}</th>
        <th>{{ trans('admin.student_management.notes') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($students as $student)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <img src="{{ $student->avatarUrl() }}" width="48" height="48" alt="{{ $student->name }}">
                <br>
                {{ $student->avatarUrl() }}
            </td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->phone }}</td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->gender ? trans('admin.student_management.' . $student->gender) : '' }}</td>
            <td>{{ $student->birth_date?->format('Y-m-d') }}</td>
            <td>{{ $student->guardian_name }}</td>
            <td>{{ $student->guardian_phone }}</td>
            <td>{{ trans('admin.student_management.' . $student->status) }}</td>
            <td>{{ $student->notes }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
