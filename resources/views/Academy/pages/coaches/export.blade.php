<table>
    <thead>
    <tr>
        <th>Partner</th>
        <th>Licence</th>
        <th>Partner Name</th>
        <th>Training Count</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>

    @foreach($coachesExport as $coach)
        <tr>
            <td>{{$coach->name ?? 'empty'}}</td>
            <td>{{$coach->license ?? 'empty'}}</td>
            <td>{{$coach->academy->commercial_name ?? 'empty'}}
            <td>{{$coach->academy->trainings?->count() ?? 'empty'}}</td>
            <td>{{ $coach->active ? 'Active' : 'InActive'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
