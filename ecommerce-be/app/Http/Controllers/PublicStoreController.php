<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\StoreIndexRequest;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\PublicStore\IndexResource;
use App\Http\Resources\PublicStore\ShowResource;
use Illuminate\Database\Eloquent\Model;

class PublicStoreController extends ApiController
{

    
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = StoreIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
    }

    public function index(): IndexResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->applyFilters();
        return $this->getResource();
    }

    public function show( int $id ) : ShowResource {
        $this->result = $this->repository->where( 'id', $id )->get()->first();
        return $this->showResource();
    }

    public function getResource(): IndexResource
    {
        return new IndexResource($this->result);
    }

    public function showResource(): ShowResource {
        return new ShowResource($this->result);
    }

}
