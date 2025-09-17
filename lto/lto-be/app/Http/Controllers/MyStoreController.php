<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreRequest;
use App\Repositories\MyStoreRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\BaseResource;

class MyStoreController extends ApiController
{
    public function __construct( MyStoreRepository $repository ) {
        $this->model            = Store::class;
        $this->repository       = $repository;
        $this->indexRequest     = BaseIndexRequest::class;
        $this->storeRequest     = StoreRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }
    
    protected function getResource() : BaseResource {
        return new BaseResource( $this->result );
    }
}
