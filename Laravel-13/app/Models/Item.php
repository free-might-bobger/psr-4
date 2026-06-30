<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Slug;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;


class Item extends Model implements Auditable
{
    use HasFactory, Slug, OptimusRequiredToModel, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'name', 'description', 'store_id', 'category_id', "delivery_charge_id"
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['slug_name', 'optimus_id', 'primary_img', 'plain_desc'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'store_id'
    ];

    public function images(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id');
    }

    public function itemPrice(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemPrice::class, 'item_id', 'id');
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function getPrimaryImgAttribute(): ?Image
    {
        if(count($this->images) > 0){
            return $this->images->filter(function($v){
                return $v->is_primary == true;
            })->first();
        }
        return null;
    }

    public function getPlainDescAttribute(): string
    {
        $stripTags =  strip_tags( $this->description );
        return str_replace("&nbsp;", '', $stripTags);
    }

   

}
