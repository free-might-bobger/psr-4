<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'images';
    protected $fillable = [
        'thumbnail', 'path', 'imageable_id', 'imageable_type',
        'is_primary', 'name', 'size'
    ];

    protected $appends = ['path_url', 'path_thumbnail'];
    
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

        $whitelist = array(
            '127.0.0.1',
            '::1',
        );
        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return url('/api/' . $this->path);
        } else {
            return url($this->path);
        }

    }

    public function getPathThumbnailAttribute()
    {

        $whitelist = array(
            '127.0.0.1',
            '::1',
        );
        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return url('/api/' . $this->path);
        } else {
            return url($this->path);
        }

    }
}
