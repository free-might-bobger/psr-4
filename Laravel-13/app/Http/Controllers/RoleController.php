<?php

namespace App\Http\Controllers;

use App\Repositories\Role\RoleRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Role;

class RoleController extends ApiController
{
    public function __construct(RoleRepository $repository) {
        $this->model =  Role::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
