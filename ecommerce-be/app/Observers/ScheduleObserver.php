<?php

namespace App\Observers;
use App\Models\Schedule;
use App\Models\ScheduleDay;
class ScheduleObserver
{
    public function created(Schedule $schedule){
        $request = app()->make('request');
        foreach($request->days as $day){
            ScheduleDay::create([
                'schedule_id' => $schedule->id,
                'name'  => $day
            ]);
        }
    }
}
