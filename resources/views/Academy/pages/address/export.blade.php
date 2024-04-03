<table>
    <thead>
    <tr>
        <th>Country</th>
        <th>City</th>
        <th>Area</th>
        <th>Academy</th>
        <th>Longitude</th>
        <th>Latitude</th>
        <th>Address</th>
    </tr>
    </thead>
    <tbody>
    @foreach($addresses as $address)
        <tr>
            <td>{{ $address->country->name ?? 'null' }}</td>
            <td>{{ $address->city->name ?? 'null' }}</td>
            <td>{{ $address->area->name ?? 'null' }}</td>
            <td>{{ $address->academy->commercial_name ?? 'null' }}</td>
            <td>{{ $address->longitude ?? 'null' }}</td>
            <td>{{ $address->latitude ?? 'null' }}</td>
            <td>{{ $address->address ?? 'null' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
