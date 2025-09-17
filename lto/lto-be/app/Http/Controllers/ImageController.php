<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Image\ImageRepository;

class ImageController extends Controller
{
    protected $repository;
    protected $indexRequest;
    protected $storeRequest;

    public function __construct(ImageRepository $repository, Request $request){
        $this->repository = $repository;
        $this->indexRequest = $request;
        $this->storeRequest = $request;
    }

    public function store(){

        $request = app()->make('request');
        $query = $request->query->all();
        $model = '\App\Models\\' . $query['entity'];
        $model = $model::where('id',  $query['id'])->first();
     
        if($request->deletedFiles){
            $deletedFiles = explode(',', $request->deletedFiles);
            $deletedFiles = collect($deletedFiles)->filter(function($value){
                return $value!= '';
            })->all();


            foreach($deletedFiles as $file){
                $image = Image::where('id', $file)->first();
                $path = public_path() . '/' . $image->path;
                if(File::exists($path)){
                    File::delete($path);
                }
                $image->delete();
            }
           
        }

        return response()->json([
            'success' =>  $this->repository->setModel($model)->setRequest($request)->upload()
        ]);

    }
}
