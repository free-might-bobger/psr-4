<?php

namespace App\Http\Controllers;

use App\Repositories\DeliveryRepository;
use App\Http\Requests\DeliveryRequest;

class DeliveryController extends ApiController
{

    public function __construct(DeliveryRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = DeliveryRequest::class;
    }

}