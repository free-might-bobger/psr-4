<?php

namespace App\Http\Controllers;

use App\Repositories\RoleUserRepository;
use App\Models\RoleUser;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\RoleUserRequest;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RoleUserService;
use App\Http\Resources\RoleUser\IndexResource;

class RoleUserController extends ApiController
{
    public function __construct(RoleUserRepository $repository, RoleUserService $service) {
        $this->model =  RoleUser::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest    = RoleUserRequest::class;
        $this->updateRequest    = RoleUserRequest::class;
        $this->service = $service;
    }

    public function destroyByPair(RoleUserRequest $request): Response
    {
        $this->service->destroyByPair($request->user_id, $request->role_id);
        return response()->noContent();
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new ShowResource($this->result);
    }
}
