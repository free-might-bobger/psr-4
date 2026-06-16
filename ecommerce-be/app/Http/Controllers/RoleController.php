<?php

namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Role;
use App\Http\Resources\Role\IndexResource;
class RoleController extends ApiController
{
    public function __construct(RoleRepository $repository) {
        $this->model =  Role::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new ShowResource($this->result);
    }
}
