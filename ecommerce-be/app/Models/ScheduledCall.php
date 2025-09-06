<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;

class ScheduledCall extends Model implements Auditable
{
    use HasFactory, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'scheduled_calls';
    
    protected $fillable = [
        'created_by',
        'updated_by',
        'date',
        'title',
        'desc',
        'security_question_id',
        'security_answer',
        'scheduled_call_status_id'
    ];

    protected $appends = ['formatted_date', 'optimus_id'];

    public function getFormattedDateAttribute(){
        return Carbon::parse($this->date)->toDayDateTimeString();
    }

    public function securityQuestion(){
        return $this->hasOne(SecurityQuestion::class, 'id', 'security_question_id');
    }

    public function scheduledCallStatus(){
        return $this->hasOne(ScheduledCallStatus::class, 'id', 'scheduled_call_status_id');
    }

    

    
}
