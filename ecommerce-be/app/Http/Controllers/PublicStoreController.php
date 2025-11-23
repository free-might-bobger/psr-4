<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicStoreResource;
use App\Repositories\StoreRepository;
use App\Http\Requests\BaseIndexRequest; 
use App\Http\Requests\Store\StoreRequest;
use App\Http\Requests\Store\StoreIndexRequest;
use Illuminate\Support\Arr;
use App\Http\Resources\BaseResource;

class PublicStoreController extends ApiController
{
    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
        $this->indexRequest = StoreIndexRequest::class;
        $this->storeRequest = StoreRequest::class;
    }

    public function index(): BaseResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->getWithinKm();
        return $this->getResource();
    }

   

    public function show( int $id ) : BaseResource {
        $this->result = $this->repository->where( 'id', (int) $id )->first();
        return $this->getResource();
    }

}
