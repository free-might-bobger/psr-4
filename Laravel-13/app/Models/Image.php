<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusId;

class Image extends Model
{
    use HasFactory, OptimusId;
    protected $table = 'images';
    protected $fillable = [
        'thumbnail', 'path', 'imageable_id', 'imageable_type',
        'is_primary', 'name', 'size'
    ];

    protected $appends = ['path_url', 'path_thumbnail', 'optimus_id'];
    
    public function imageable()
    {
        return $this->morphTo();
    }


    public function getIsPrimaryAttribute($val)
    {
        return (Integer) $val;
    }

    public function getPathUrlAttribute()
    {
        $path = $this->path;
        if (str_contains(env('APP_URL', ''), 'mynearshops')) {
            $path = 'public/' . $this->path;
        }
        return url($path);
    }

    public function getPathThumbnailAttribute()
    {
        $path = $this->path;
        if (str_contains(env('APP_URL', ''), 'mynearshops')) {
            $path = 'public/' . $this->path;
        }
        return url($path);
    }
}
