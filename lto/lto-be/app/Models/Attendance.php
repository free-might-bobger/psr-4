<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'attendance';
    protected $fillable = ['login', 'logout', 'user_id'];
    protected $appends = [ 'optimus_id', 'loginDate'];

    public function getCreatedAtAttribute($val): string {
        return substr(Carbon::parse($val)->setTimezone('Asia/Manila')->toDayDateTimeString(), 17);
    }

    public function getUpdatedAtAttribute($val): string {
        if($this->logout){
            return Carbon::parse($val)->setTimezone('Asia/Manila')->toDayDateTimeString();
        }
        return '';
    }

    public function getLogoutAttribute($val): string {
        if($val){
            return Carbon::parse($val)->setTimezone('Asia/Manila')->toDayDateTimeString();
        }
        return '';
    }

    public function getLoginDateAttribute(): string {
        return str_replace("12:00 AM", "", Carbon::parse($this->login)->setTimezone('Asia/Manila')->toDayDateTimeString());
    }


    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
