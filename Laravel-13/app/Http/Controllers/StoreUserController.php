<?php

namespace App\Http\Controllers;

use App\Repositories\StoreUserRepository;
use App\Http\Requests\StoreUser\IndexRequest;
use App\Http\Requests\StoreUser\StoreRequest;
use App\Http\Requests\StoreUser\UpdateRequest;

class StoreUserController extends ApiController
{
    public function __construct(StoreUserRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
    }

}
