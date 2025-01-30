<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Training</th>
        <th>User</th>
        <th>Price</th>
        <th>Order Number</th>
        <th>Status</th>
        <th>User Type</th>
        <th>Payment Status</th>
        <th>Is Refund</th>
        <th>Child Type</th>
        <th>School Name</th>
        <th>Parent Name</th>
        <th>Parent Phone</th>
        <th>Coach Preference</th>
        <th>Frequent Attendance</th>
        <th>Relation with child</th>
        <th>Referral Source</th>
        <th>Medical Condition</th>
        <th>Medical Condition Details</th>
        <th>Additional Information</th>
        <th>Delivery Service</th>
        <th>Club member</th>
        <th>Start Date</th>



    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $join->id }}</td>
        <td>{{ $join->training->name }}</td>
        <td>{{ $join->user->name }}</td>
        <td>{{ $join->price }}</td>
        <td>{{ $join->status }}</td>
        <td>{{ $join->invoice->user_type }}</td>
        <td>{{ $join->invoice->payment_status }}</td>
        <td>{{ $join->invoice->is_refunded }}</td>
        <td>{{ $join->user->child_type }}</td>
        <td>{{ $join->user->school_name }}</td>
        <td>{{ $join->user->parent_name }}</td>
        <td>{{ $join->user->parent_phone }}</td>
        <td>{{ $join->user->coach_preference }}</td>
        <td>{{ $join->user->frequent_attendance }}</td>
        <td>{{ $join->user->relation_with_child }}</td>
        <td>{{ $join->user->referral_source }}</td>
        <td>{{ $join->user->medical_condition }}</td>
        <td>{{ $join->user->medical_condition_details }}</td>
        <td>{{ $join->user->additional_information }}</td>
        <td>{{ $join->user->delivery_service }}</td>
        <td>{{ $join->user->club_member }}</td>
        <td>{{ $join->user->start_date }}</td>
    </tr>
    </tbody>
</table>
