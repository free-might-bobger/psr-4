<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Http\Requests\FindStoreRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\FindStoreResource;

class FindStoreController
{
    private $repository;
    private $result;
    
    public function __construct(StoreRepository $storeRepository)
    {
        $this->repository = $storeRepository;
    }
    
    public function findStore(FindStoreRequest $findStoreRequest): FindStoreResource
    {
        $this->result = $this->repository->setParameters($findStoreRequest->all())->applyFilters();
        return $this->getResource();
    }

    public function show(int $id): FindStoreResource {
        $this->result = $this->repository->findOrFail($id);
        return $this->getResource();
    }

    public function getResource(){
        return new FindStoreResource($this->result);
    }


}
