<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\DeliveryCharge\IndexResource;
use App\Repositories\DeliveryChargeRepository;
use App\Traits\Obfuscate\OptimusId;
class DeliveryChargeController extends ApiController
{
    use OptimusId;

    public function __construct(DeliveryChargeRepository $repository)
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
