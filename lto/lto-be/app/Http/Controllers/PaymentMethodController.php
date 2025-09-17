<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentMethodRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\PaymentMethod;

class PaymentMethodController extends ApiController
{
    public function __construct(PaymentMethodRepository $repository) {
        $this->model =  PaymentMethod::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
