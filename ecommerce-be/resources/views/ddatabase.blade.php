<table>
    <thead>
        <tr></tr>
        <tr></tr>
        <tr>
            <td>
                <h3>ENROLEES DATABASE</h3>
            </td>
        </tr>
        <tr>
            <td>LRN</td>
            <td>Id No.</td>
            <td>Firstname</td>
            <td>Middlename</td>
            <td>Lastname</td>
            <td>Contact Number</td>
            <td>Course</td>
            <td>Time</td>
            <td>School Name</td>
            <td>Address</td>
        </tr>
    </thead>
    <tbody>

        @foreach($confirmEnrolled as $c)
        <tr>
            <td>{{ $c->enrollee->lrn }}</td>
            <td>{{ $c->enrollee->idno }}</td>
            <td>{{ $c->enrollee->firstname }}</td>
            <td>{{ $c->enrollee->middlename }}</td>
            <td>{{ $c->enrollee->lastname }}</td>
            <td>{{ $c->enrollee->mobile }}</td>
            <td>{{ $c->course['name'] }}</td>
            <td>{{ $c->schedule->name }}</td>
            <td>{{ $c->enrollee->name_of_school }}</td>
            <td>{{ $c->enrollee->school_address }}</td>
        </tr>
        @endforeach
    </tbody>
</table>