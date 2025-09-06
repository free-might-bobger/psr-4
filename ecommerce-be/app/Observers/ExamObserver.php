<?php

namespace App\Observers;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Arr;
class ExamObserver
{
    public function saved(Exam $exam){
        $request = app()->make('request');
        foreach($request->questions as $question){
           $newQuestion =  Question::create([
                'exam_id'       => $exam->id,
                'question_id'   => Arr::get($question, 'question_id'),
                'text'          => Arr::get($question, 'text'),
            ]);
            foreach(Arr::get($question, 'options') as $option){
                $option['question_id'] = $newQuestion->id;
                QuestionOption::create($option);
            }
        }
        $exam->created_by = \Auth::User()->id;
        $exam->schedules()->attach($request->selectedSchedules);
        $exam->saveQuietly();
    }
}
