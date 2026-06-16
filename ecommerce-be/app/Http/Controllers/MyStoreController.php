<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\PatchRequest;
use App\Repositories\MyStoreRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\MyStore\IndexResource;
use App\Models\Store;
class MyStoreController extends ApiController
{
    public function __construct( MyStoreRepository $repository ) {
        $this->model            = Store::class;
        $this->repository       = $repository;
        $this->indexRequest     = BaseIndexRequest::class;
        $this->storeRequest     = StoreRequest::class;
        $this->updateRequest    = PatchRequest::class;
    }
    
    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }
}
