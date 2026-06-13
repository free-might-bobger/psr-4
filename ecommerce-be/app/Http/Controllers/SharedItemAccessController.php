<?php

namespace App\Http\Controllers;

use App\Http\Resources\SharedItemAccess\IndexResource;
use App\Repositories\ItemRepository;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Http\Request;

class SharedItemAccessController extends ApiController
{

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = Request::class;
    }

    public function getResource(): IndexResource{
        return new IndexResource($this->result);
    }

    public function showResource(): IndexResource {
        return new IndexResource($this->result);
    }

    
}
