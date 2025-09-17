<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class PickupTime extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pickup_time';
    protected $fillable = [
        'transaction_id',
        'store_id',
        'date_time'
    ];

    protected $appends = ['formatted_date_time'];

    public function getFormattedDateTimeAttribute(){
        if($this->date_time){
            return Carbon::parse($this->date_time)->toDayDateTimeString();
        }
    }
}
