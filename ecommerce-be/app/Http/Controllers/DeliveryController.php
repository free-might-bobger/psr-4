<?php

namespace App\Http\Controllers;

use App\Repositories\DeliveryRepository;
use App\Http\Requests\DeliveryRequest;
use App\Http\Resources\BaseResource;

class DeliveryController extends ApiController
{

    public function __construct(DeliveryRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = DeliveryRequest::class;
    }

     public function index(): BaseResource
    {
        $this->params = app($this->indexRequest)->all();
        $this->result = $this->repository->setParameters($this->params)->applyFilters();
        return $this->getResource();
    }

}