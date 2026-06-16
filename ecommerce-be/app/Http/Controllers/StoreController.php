<?php

namespace App\Http\Controllers;

use App\Repositories\Store\StoreRepository;
use App\Http\Requests\Store\IndexRequest;
use App\Http\Requests\Store\StoreRequest;
use App\Models\Store;
use App\Http\Resources\Store\IndexResource;

class StoreController extends ApiController
{
    public function __construct(StoreRepository $repository){
        $this->model =  Store::class;
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
        $this->showRequest = StoreRequest::class;
        $this->updateRequest = StoreRequest::class;
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }

    
}