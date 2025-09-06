<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Slug;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, Slug, OptimusRequiredToModel;

    protected $table = 'categories';
    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'desc'
    ];
    protected $appends = ['label', 'value'];

    public function items(): HasMany {
        return $this->hasMany('App\Models\Item', 'category_id', 'id');
    }

}
