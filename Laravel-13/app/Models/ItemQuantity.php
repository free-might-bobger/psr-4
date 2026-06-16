<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable;

class ItemQuantity extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'item_quantity';

    protected $fillable = [
        'item_price_id',
        'qty'
    ];

    public function getCreatedAtAttribute($val){
        return Carbon::parse($val)->toDayDateTimeString();
    }
}