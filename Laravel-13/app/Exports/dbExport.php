<?php

namespace App\Exports;

use App\Model\ConfirmEnrolled;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class dbExport implements FromView
{
    public function view(): View
    {

        return view('ddatabase', [
            'confirmEnrolled' => ConfirmEnrolled::with(['enrollee', 'course', 'schedule'])
                ->orderBy('created_at', 'DESC')->get(),
        ]);
    }
}