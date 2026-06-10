<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Http\Requests\Store\StoreIndexRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\PublicStoreResource;

class FindStoreController extends ApiController
{

    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = StoreIndexRequest::class;
    }

    public function index(): BaseResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->applyFilters();
        return $this->getResource();
    }

     public function getResource(): BaseResource
    {
        return new $this->baseResourceClass($this->result);
    }

    public function showResourceClass(): PublicStoreResource
    {
        return new PublicStoreResource($this->result);
    }

}
