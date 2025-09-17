<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MyFranchisee extends Franchisee implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'franchisees';
    protected $fillable = [
        'name', 'refprovince_id', 'refcitymun_id', 'user_id', 'mobile'
    ];
    protected $appends = ['optimus_id'];

   
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
