<?php 

namespace App\Traits;

trait Slug
{
    
    public function getSlugNameAttribute()
    {
        return str_slug($this->name);
    }
}