<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, OptimusRequiredToModel, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'mobile',
        'passcode',
        'status',
        'mobile_attempt_count',
        'mobile_verified_at',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['optimus_id', 'label', 'value'];

    public function roles(){
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function scopeWithRolesMenus($q){
        return $q->with(['roles.menus']);
    }


    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id');
    }

    public function address()
    {
        return $this->morphMany(Address::class, 'addressable', 'addressable_type', 'addressable_id');
    }

    public function getDefaultAddressAttribute() {
        return $this->address->first();
    }

    public function enrollees(): HasMany {
        return $this->hasMany(Enrollee::class, 'user_id', 'id');
    }

    
}
