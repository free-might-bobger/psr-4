<?php

namespace App\Repositories;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(new Image);
    }
    

    /**
     * GENERIC UPDATE IMAGE
     * @param Request $request
     * @return void
     * this can be move to Baserepository later when someone would use it.
     */
    public function updatePrimaryImage(Request $request){
        $model = '\App\Models\\' . $request->entity;
        $model = $model::where('id', $request->id)->first();
        $model->images()->where('name', $request->primaryName)->update(['is_primary' => true]);
        $model->images()->where('name', '!=', $request->primaryName)->update(['is_primary' => false]);
    }
    
}
