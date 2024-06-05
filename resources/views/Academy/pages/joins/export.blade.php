<table>
    <thead>
    <tr>
        <th>training</th>
        <th>partner_name</th>
        <th>level</th>
        <th>sport</th>
        <th>age_group</th>
        <th>classes</th>
        <th>start_date</th>
        <th>end_date</th>
        <th>coach</th>
        <th>count</th>
        <th>max_player</th>
        <th>price</th>
        <th>discount_price</th>
    </tr>
    </thead>
    <tbody>
    @foreach($joins as $join)
        <tr>
            <td>{{$join->training->name ?? 'empty'}}</td>
            <td>{{$join->training->academy->commercial_name ?? 'empty'}}</td>
            <td>{{$join->training->sport->name ?? 'empty'}}</td>
            <td>{{$join->training->level ?? 'empty'}}</td>
            <td>{{$join->training->age_group ?? 'empty'}}</td>
            <td>{{$join->training->classes->count() ?? 'empty'}}</td>
            <td>{{$join->training->start_date ?? 'empty'}}</td>
            <td>{{$join->training->end_date ?? 'empty'}}</td>
            <td>{{$join->training->coach->name ?? 'empty'}}</td>
            <td>{{$join->training->joins->count() ?? 'empty'}}</td>
            <td>{{$join->training->max_players ?? 'empty'}}</td>
            <td>{{$join->training->price ?? 'empty'}}</td>
            <td>{{$join->training->discount_price ?? 'empty'}}</td>

        </tr>
    @endforeach
    </tbody>
</table>
