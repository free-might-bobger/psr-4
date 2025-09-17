<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseIndexRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\DeliveryChargeRepository;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class DeliveryChargeController extends ApiController
{
    use OptimusRequiredToModel;

    public function __construct(DeliveryChargeRepository $repository)
    {

        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
    }

    public function getResource()
    {
        return new BaseResource($this->result);
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }

}
