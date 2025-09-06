<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentOption\PaymentOptionRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\PaymentOption;

class PaymentOptionController extends ApiController
{
    public function __construct(PaymentOptionRepository $repository) {
        $this->model =  PaymentOption::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
