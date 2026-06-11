<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\ReceiveMethod\IndexResource;
use App\Repositories\ReceiveMethod\ReceiveMethodRepository;

class ReceiveMethodController extends ApiController
{
    public function __construct(ReceiveMethodRepository $repository)
    {

        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }
}
