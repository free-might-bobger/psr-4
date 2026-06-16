<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store\StoreRequest;
use App\Repositories\SharedStoreAccessRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\BaseResource;
use App\Models\Store;
use App\Http\Resources\SharedStoreAccess\IndexResource;
class SharedStoreAccessController extends ApiController
{
    public function __construct( SharedStoreAccessRepository $repository ) {
        $this->repository       = $repository;
        $this->indexRequest     = BaseIndexRequest::class;
        $this->showRequest     = BaseIndexRequest::class;
    }

    public function getResource(): IndexResource {
        return new IndexResource($this->result);
    }

    public function showResource(): IndexResource {
         return new IndexResource($this->result);
    }
    
}
