<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ImageRepository;
use App\Models\Image;
class ImageController extends ApiController
{
    public function __construct(ImageRepository $repository)
    {

        $this->repository = $repository;
        $this->model = Image::class;
    }

    public function updatePrimaryImage(Request $request){

       $this->repository->updatePrimaryImage($request);

        return response()->json([
            'success' => true
        ]);

    }

}
