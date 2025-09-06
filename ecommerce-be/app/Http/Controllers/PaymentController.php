<?php

namespace App\Http\Controllers;

use App\Repositories\Payment\PaymentRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Payment;

class PaymentController extends ApiController
{
    public function __construct(PaymentRepository $repository) {
        $this->model =  Payment::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }
}
