<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class Exam extends Model
{
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'exams';
    protected $fillable = [
        'directions', 'time_limit', 'subject_id', 'created_by'
    ];

    protected $appends = [ 'optimus_id', 'name'];

    public function subject():HasOne {
        return $this->hasOne( Subject::class, 'id', 'subject_id' );
    }

    public function getNameAttribute(){
        if($this->subject){
            return $this->subject->name;
        }
    }

    public function schedules() {
        return $this->belongsToMany(Schedule::class, 'exam_schedule', 'exam_id', 'schedule_id');
    }

    public function questions() {
        return $this->hasMany(Question::class, 'exam_id', 'id');
    }
}
