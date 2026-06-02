<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;

class StoreAdvertisement extends Model implements Auditable
{
    use HasFactory, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_advertisements';
    protected $fillable = [
        'franchisee_id',
        'rank',
        'store_id',
        'start_at',
        'end_at',
        'amount',
        'city_id'
    ];

    protected $appends = ['optimus_id'];
    
    public function store(){
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function getStartAtAttribute($value){
        return Carbon::parse($value)->toDayDateTimeString();
    }

    public function getEndAtAttribute($value){
        return Carbon::parse($value)->toDayDateTimeString();
    }
}
