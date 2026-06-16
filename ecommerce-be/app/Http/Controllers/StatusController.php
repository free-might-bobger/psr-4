<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Repositories\StatusRepository;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;
use App\Http\Resources\Status\IndexResource;

class StatusController extends ApiController
{

    public function __construct(StatusRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }

     public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }

}
