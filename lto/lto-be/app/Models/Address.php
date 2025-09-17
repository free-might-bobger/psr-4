<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
class Address extends Model
{
    use HasFactory, OptimusRequiredToModel, SoftDeletes;

    protected $table = 'address';
    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'province_id',
        'city_id',
        'street_lot_blk',
    ];

    protected $hidden = ['addressable_id', 'addressable_type'];

    protected $appends = ['default_address', 'optimus_id' ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
    
    public function province()
    {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }
    public function city()
    {
        return $this->hasOne('App\Models\City', 'id', 'city_id');
    }
   

    public function getDefaultAddressAttribute()
    {
        $completeAddress = [];

        if ($this->city) {
            $completeAddress[] = $this->city->name;
        }
        if ($this->province) {
            $completeAddress[] = $this->province->name;
        }

        return implode(', ', $completeAddress);
    }

    
}
