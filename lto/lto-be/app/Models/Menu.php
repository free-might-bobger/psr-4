<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Slug;
use App\Traits\Models\LabelValue;

class Menu extends Model 
{
    use HasFactory, Slug, LabelValue;
    
    protected $table = 'menus';
    protected $fillable = ['name', 'parent_id'];
    protected $appends = ['slug_name', 'label', 'value'];

    
    public function roles(){
        return $this->belongsToMany(Role::class, 'menu_role', 'menu_id', 'role_id');
    }

    public function children()
    {

        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }


    
}
