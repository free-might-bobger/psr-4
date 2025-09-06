<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Slug;
use OwenIt\Auditing\Contracts\Auditable;

class StaticMenu extends Model implements Auditable
{
    use HasFactory, Slug;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'static_menus';
    protected $fillable = ['name', 'parent_id'];
    protected $appends = ['slug_name'];

    
    public function roles(){
        return $this->belongsToMany(Role::class, 'menu_role', 'role_id', 'menu_id');
    }

    public function children()
    {

        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function getLabelAttribute()
    {
        return $this->name;
    }
}
