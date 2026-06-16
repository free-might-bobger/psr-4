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
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'lastname',
        'email',
        'mobile',
        'activation_code',
        'status',
        'mobile_attempt_count',
        'mobile_verified_at',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['optimus_id', 'label', 'value'];

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function scopeWithRolesMenus($query): void
    {
        $query->with(['roles.menus']);
    }

    public function getLabelAttribute(): string
    {
        return $this->name . ' (' . $this->mobile . ')';
    }

    public function getValueAttribute(): int
    {
        return $this->id;
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id');
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'addressable', 'addressable_type', 'addressable_id');
    }

    public function getDefaultAddressAttribute(): ?Address
    {
        return $this->address->first();
    }

    public function enrollees(): HasMany {
        return $this->hasMany(Enrollee::class, 'user_id', 'id');
    }


    
}
