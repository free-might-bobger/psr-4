<?php 

namespace App\Traits\Models;
use Carbon\Carbon;

trait CreatedAttribute
{

    public function getCreatedAtAttribute($val){

        return Carbon::parse($val)->toDayDateTimeString();
    }
}