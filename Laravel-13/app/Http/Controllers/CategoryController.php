<?php

namespace App\Http\Controllers;

use App\Repositories\Category\CategoryRepository;
use App\Http\Resources\BaseResource;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\BaseIndexRequest;

class CategoryController extends ApiController
{

    public function __construct(CategoryRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = CategoryRequest::class;
        $this->updateRequest = CategoryRequest::class;
    }

}