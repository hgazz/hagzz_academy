<table>
    <thead>
    <tr>
        <th>Academy</th>
        <th>Training</th>
        <th>Sport</th>
        <th>Class</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($classes as $class)
        <tr>
            <td>{{ $class->training->academy->commercial_name ?? 'null' }}</td>
            <td>{{ $class->training->name ?? 'null' }}</td>
            <td>{{ $class->sport->name ?? 'null' }}</td>
            <td>{{ $class->title ?? 'null' }}</td>
            <td>{{ $class->date ?? 'null' }}</td>
            <td>{{ $class->start_time ?? 'null' }}</td>
            <td>{{ $class->start_time ?? 'null' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
