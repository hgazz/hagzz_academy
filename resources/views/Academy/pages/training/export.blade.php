<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>start Date</th>
        <th>End Date</th>
        <th>Description</th>
        <th>Coach</th>
        <th>active</th>
        <th>Sport</th>
        <th>Academy</th>
        <th>Max Players</th>
        <th>Level</th>
        <th>Gender</th>
        <th>Age Group</th>
        <th>Address</th>
        <th>discount_price</th>


    </tr>
    </thead>
    <tbody>
    @foreach($trainingExports as $training)
        <tr>
            <td>{{ $training->id }}</td>
            <td>{{ $training->name }}</td>
            <td>{{ $training->start_date }}</td>
            <td>{{ $training->end_date }}</td>
            <td>{{ $training->description }}</td>
            <td>{{ $training->coach->name ?? 'null' }}</td>
            <td>{{ $training->active }}</td>
            <td>{{ $training->sport->name ?? 'null'}}</td>
            <td>{{ $training->academy->commercial_name ?? 'null' }}</td>
            <td>{{ $training->max_players }}</td>
            <td>{{ $training->level }}</td>
            <td>{{ $training->gender }}</td>
            <td>{{ $training->age_group }}</td>
            <td>{{ $training->address->address ?? 'null' }}</td>
            <td>{{$training->discount_price}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
