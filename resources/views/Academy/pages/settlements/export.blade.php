<table>
    <thead>
    <tr>
        <th>Partner</th>
        <th>total_amount</th>
        <th>settlement_date</th>
        <th>status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($settlements as $settlement)
        <tr>
            <td>{{$settlement->partner->name ?? 'empty'}}</td>
            <td>{{$settlement->total_amount ?? 'empty'}}</td>
            <td>{{$settlement->settlement_date ?? 'empty'}}</td>
            <td>{{$settlement->status ?? 'empty'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
