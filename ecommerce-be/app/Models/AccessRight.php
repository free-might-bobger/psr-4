<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRight extends Model
{
    use HasFactory;

    protected $table = 'access_rights';

    protected $appends = ['label', 'value'];
  
    public function getLabelAttribute()
    {
        return $this->name;
    }

    public function getValueAttribute()
    {
        return $this->id;
    }

    public function menuRole(){
        return $this->belongsToMany(MenuRole::class, 'menu_role_access_right', 'access_right_id', 'menu_role_id')->using(MenuRoleAccessRight::class);
    }

}
