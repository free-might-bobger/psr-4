<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class DeliveryFranchisee extends Model
{
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'delivery_franchisee';
    protected $fillable = [
        'user_id', 'franchisee_id', 'refprovince_id', 'refcitymun_id', 'mobile'
    ];

    protected $appends = ['optimus_id'];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id')->select(['name', 'id']);
    }
    public function province(){
        return $this->hasOne(Province::class, 'id', 'refprovince_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'refcitymun_id');
    }
}
