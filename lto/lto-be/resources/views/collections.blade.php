<table>
    <thead>
    <tr></tr>
    <tr></tr>
    <tr>
        <td><h3>DAILY COLLECTION REPORT</h3></td>
    </tr>
    <tr>
        <th>From: {{ $startDate }}</th>
        <th>To: {{ $endDate }}</th>
    </tr>
    </thead>
    <tbody>
    <tr></tr>
    <tr></tr>
    <tr>
        <th>OR NO</th>
        <th>Lastname</th>
        <th>Firstname</th>
        <th>STD No:</th>
        <th>Course</th>
        <th>Balance Name</th>
        <th>Paid Amount</th>
        <th>Date Paid</th>
		<th>Remarks</th>
        <th>SU</th>
        <th>ID</th>
        <th>Old Balance</th>

    </tr>
    @if($request->payment == 'true')
        @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->prefix . '-' . $payment->receipt_no }}</td>
                <td>{{ $payment->confirmEnrolled->enrollee->lastname }}</td>
                <td>{{ $payment->confirmEnrolled->enrollee->firstname }}</td>
                <td></td>
                <td></td>
                <td>{{ $payment->balance->name }}</td>
                <td>{{$payment->amount_charge}}</td>
                <td>{{$payment->created_at}}</td>
            </tr>
        @endforeach
    @endif
    @if($request->trainingPayment == 'true')
         @foreach($trainingPayments as $tp)
            <tr>
                <td>{{ $tp->prefix . '-' . $tp->receipt_no }}</td>
                <td>{{ $tp->lastname }}</td>
                <td>{{ $tp->firstname }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{$tp->paid_amount}}</td>
                <td>{{$tp->created_at}}</td>
				 <td>{{$tp->remarks}}</td>
            </tr>
        @endforeach
    @endif
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td> TOTAL COLLECTION</td>
            <td>&#8369; {{ number_format($payments->sum('amount_charge') + $trainingPayments->sum('paid_amount')) }}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td>I. TOTAL COLLECTION</td>
            <td>&#8369; {{ number_format($payments->sum('amount_charge') + $trainingPayments->sum('paid_amount'),  2, '.', ',') }}</td>
        </tr>
        <tr>
            <td>GROSS FOR TODAY</td>
        </tr>
        <tr>
            <td>LESS EXPENSES FOR TODAY</td>
        </tr>
        <tr>
            <td></td>
            <td>ITEMIZED AT THE BACK:</td>
            <td>________________________</td>
        </tr>
        <tr>
            <td>TOTAL NET COLLECTION</td>
        </tr>

        <tr>
            <td>AMOUNT RECEIVED BY:</td>
            <td></td>
            <td></td>
            
            <td>PREPARED BY</td>
        </tr>
        <tr>
            <td>____________________________</td>
            <td></td>
            <td></td>
            
            <td>_____________________________</td>
        </tr>
        <tr>
            <td>CHECKED BY:</td>
            <td></td>
            <td></td>
            <td>AUDITED BY:</td>
        </tr>
        <tr>
            <td>____________________________</td>
            <td></td>
            <td></td>
            <td>_____________________________</td>
        </tr>
    </tbody>
</table>