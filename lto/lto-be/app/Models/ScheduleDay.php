<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDay extends Model
{
    use HasFactory;

    protected $table = 'schedule_days';
    protected $fillable = [
        'name', 'schedule_id'
    ];

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }
}
