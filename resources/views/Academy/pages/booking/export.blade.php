<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Phone</th>
        <th>training</th>
        <th>Order Number</th>
        <th>Status</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->user->name ?? 'null'}}</td>
            <td>{{ $invoice->user->phone ?? 'null'}}</td>
            <td>{{ $invoice->training->name ?? 'null' }}</td>
            <td>{{ $invoice->order_number ?? 'null' }}</td>
            <td>{{ $invoice->status ?? 'null' }}</td>
            <td>{{ $invoice->amount ?? 'null'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
