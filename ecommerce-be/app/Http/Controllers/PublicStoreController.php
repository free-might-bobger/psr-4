<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\StoreIndexRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\ShowResource;
use Illuminate\Database\Eloquent\Model;

class PublicStoreController extends ApiController
{

    protected string $showResourceClass = ShowResource::class;
    
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = StoreIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
    }

    public function index(): BaseResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->applyFilters();
        return $this->getResource();
    }

    public function show( int $id ) : ShowResource {
        $this->result = $this->repository->where( 'id', $id )->get()->first();
        return $this->getShowResourceClass();
    }

    public function getResource(): BaseResource
    {
        return new ($this->result);
    }

    public function showResourceClass(): ShowResource {
        return new $this->showResourceClass($this->result);
    }

}
