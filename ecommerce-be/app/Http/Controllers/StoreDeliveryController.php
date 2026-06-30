<?php

namespace App\Http\Controllers;

use App\Repositories\DeliveryRepository;
use App\Http\Requests\StoreDelivery\IndexRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Delivery\IndexResource;

class StoreDeliveryController extends ApiController
{

    public function __construct(DeliveryRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
    }

    public function index()
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->applyFilters();
        return $this->getResource();
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }


}