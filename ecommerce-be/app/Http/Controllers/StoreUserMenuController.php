<?php

namespace App\Http\Controllers;

use App\Repositories\StoreUserMenuRepository;
use App\Http\Requests\StoreUserMenu\IndexRequest;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\StoreUserMenu\StoreRequest;
use App\Http\Requests\StoreUserMenu\UpdateRequest;
use App\Http\Resources\StoreUserMenu\IndexResource;

class StoreUserMenuController extends ApiController
{
    public function __construct(StoreUserMenuRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
    }

    public function getResource(): IndexResource {
        return new IndexResource($this->result);
    }

    public function showResource(): IndexResource {
        return new IndexResource($this->result);
    }
}
