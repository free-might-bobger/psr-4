<?php

namespace App\Exports;

use App\Model\Payment;
use App\Model\TrainingPayment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoicesExport implements FromView
{
    public function view(): View
    {
        $request = app()->make('request');

        return view('collections', [
            'payments' => Payment::where('created_at', '>=', $request->startDate)
                ->where('created_at', '<=', $request->endDate)
                ->with(['confirmEnrolled.enrollee', 'confirmEnrolled', 'balance'])
                ->get(),
            'trainingPayments' => TrainingPayment::where('created_at', '>=', $request->startDate)
                ->where('created_at', '<=', $request->endDate)
                ->get(),
            'startDate' => Carbon::parse($request->startDate)->toDayDateTimeString(),
            'endDate' => Carbon::parse($request->endDate)->toDayDateTimeString(),
            'request' => $request,
        ]);
    }
}