<?php

namespace App\Http\Controllers;

use App\Repositories\StoreMenuAccessRepository;
use App\Http\Requests\StoreMenuAccess\IndexRequest;
use App\Http\Requests\StoreMenuAccess\StoreRequest;
use App\Http\Requests\StoreMenuAccess\UpdateRequest;

class StoreMenuAccessController extends ApiController
{
    public function __construct(StoreMenuAccessRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
    }

}
