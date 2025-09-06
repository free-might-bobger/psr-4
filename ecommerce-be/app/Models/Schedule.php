<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class Schedule extends Model
{
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'schedules';

    protected $fillable = [
        'end', 'start', 'trainer_id', 'name'
    ];

    protected $appends = ['optimus_id', 'label', 'value'];

    public function scheduleDays(): HasMany {
        return $this->hasMany(ScheduleDay::class, 'schedule_id', 'id');
    }

    public function trainer(){
        return $this->hasOne(User::class, 'id', 'trainer_id');
    }

    public function getLabelAttribute(){
        return $this->name . ' (' . $this->start . '-' . $this->end. ')';
    }
    public function getValueAttribute(){
        return $this->id;
    }

   
}
