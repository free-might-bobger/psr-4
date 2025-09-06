<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\DataAccessObjectContract;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;

class StoreUser extends Model implements DataAccessObjectContract, Auditable
{
    use HasFactory, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_user';

    protected $fillable = [
        'store_id',
        'user_id'
    ];

    protected $appends = ['optimus_id'];


    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function store(){
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function storeUserMenu(){
        return $this->hasMany(StoreUserMenu::class, 'store_user_id', 'id');
    }
    
}
