<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MenuRole extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'menu_role';
    public function accessRights(){
        return $this->belongsToMany(AccessRight::class, 'menu_role_access_right', 'menu_role_id', 'access_right_id')->using(MenuRoleAccessRight::class);
    }

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

   

    
}
