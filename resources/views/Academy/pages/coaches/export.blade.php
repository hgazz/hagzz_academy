<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>description</th>
        <th>Academy</th>
        <th>license</th>
    </tr>
    </thead>
    <tbody>
    @foreach($coachesExport as $coach)
        <tr>
            <td>{{ $coach->name }}</td>
            <td>{{ $coach->description }}</td>
            <td>{{$coach->academy->commercial_name ?? 'null'}}</td>
            <td>{{ $coach->license }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
