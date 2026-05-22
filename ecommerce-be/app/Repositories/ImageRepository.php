<?php

namespace App\Repositories;
use App\Models\Image;
class ImageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(new Image);
    }
    
}
