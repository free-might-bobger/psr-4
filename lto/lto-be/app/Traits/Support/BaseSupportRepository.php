<?php 

namespace App\Traits\Support;
use App\Models\Image;

trait BaseSupportRepository
{
    
    protected function imageUploadIsPrimary($isPrimary){
        
        Image::create([
            'thumbnail' => 'images/uploads/thumbnails/' . $this->fileName,
            'path' => 'images/uploads/' . $this->fileName,
            'imageable_id' => $this->model->id,
            'imageable_type' => get_class($this->model),
            'is_primary' => true,
            'name' => $this->name,
            'size' => $this->size,
            'type' => $this->request->type
        ]);
    }
    protected function fileUploadIsPrimary(bool $isPrimary){
        
        Image::create([
            'thumbnail' => 'files/uploads/thumbnails/' . $this->fileName,
            'path' => 'files/uploads/' . $this->fileName,
            'imageable_id' => $this->model->first()->id,
            'imageable_type' => get_class($this->model->first()),
            'is_primary' => $isPrimary,
            'name' => $this->name,
            'size' => $this->size,
            'type' => $this->request->type
        ]);
    }

    

    
}