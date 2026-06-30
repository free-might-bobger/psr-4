<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Repositories\Category\CategoryRepository;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;

class CategoryController extends ApiController
{

    public function __construct(CategoryRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest = CategoryRequest::class;
        $this->updateRequest = CategoryRequest::class;
    }

    public function getResource(): CategoryResource{
        return new CategoryResource($this->result);
    }

    public function showResource(): CategoryResource {
        return new CategoryResource($this->result);
    }

}