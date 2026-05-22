<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ImageRepository;
use App\Models\Image;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    protected ImageRepository $repository;
    protected Request $indexRequest;
    protected Request $storeRequest;

    public function __construct(ImageRepository $repository, Request $request){
        $this->repository = $repository;
        $this->indexRequest = $request;
        $this->storeRequest = $request;
    }

    public function updatePrimaryImage(){

        $model = '\App\Models\\' . $this->indexRequest->entity;
        $model = $model::where('id', $this->indexRequest->id)->first();
        $model->images()->where('name', $this->indexRequest->primaryName)->update(['is_primary' => true]);
        $model->images()->where('name', '!=', $this->indexRequest->primaryName)->update(['is_primary' => false]);

        return response()->json([
            'success' => true
        ]);

    }
}
