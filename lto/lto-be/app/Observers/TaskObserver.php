<?php

namespace App\Observers;
use App\Models\Task;

class TaskObserver
{
    public function saved(Task $task){
        $request = app()->make('request');
        $scheduleIds = [];
        foreach($request->schedules as $schedule){
           $scheduleIds[] = $schedule['id'];
        }
        $task->schedules()->attach($scheduleIds);
        $task->saveQuietly();
    }
}
