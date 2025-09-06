<?php

namespace App\Http\Controllers;

use App\Repositories\Store\StoreRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\Store\StoreRequest;
use App\Models\Store;


class StoreController extends ApiController
{
    public function __construct(StoreRepository $repository){
        $this->model =  Store::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = StoreRequest::class;
    }

    protected function getResource() : BaseResource {
        return new BaseResource( $this->result );
    }

}