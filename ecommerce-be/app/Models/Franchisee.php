<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Carbon\Carbon;

class Franchisee extends Model
{
    use HasFactory, SoftDeletes, OptimusRequiredToModel;

    protected $table = 'franchisees';
    protected $fillable = [
        'name', 'refprovince_id', 'refcitymun_id', 'user_id', 'mobile'
    ];
    protected $appends = ['optimus_id'];

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->toDayDateTimeString();
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function province(){
        return $this->hasOne(Province::class, 'id', 'refprovince_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'refcitymun_id');
    }
}
