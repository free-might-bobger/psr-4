<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Slug;
use App\Traits\Models\CreatedAttribute;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class Role extends Model
{
    use HasFactory, Slug, CreatedAttribute, OptimusRequiredToModel;

    protected $table = 'roles';
    protected $fillable = ['name'];
    protected $appends = ['slug_name', 'text', 'optimus_id', 'value', 'label'];
    
    public function menus(){
        return $this->belongsToMany(Menu::class, 'menu_role', 'role_id', 'menu_id')->withPivot('id');
    }

    public function getTextAttribute(){
        return $this->name;
    }

    public function users(){
        return $this->belongsToMany(User::class, 'role_user', 'user_id', 'role_id');
    }

    public function getLabelAttribute(){
        return $this->name;
    }

    public function getValueAttribute(){
        return $this->id;
    }

   
}
