<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\Transaction\UpdateRequest;

class TransactionController extends ApiController {

    protected string $model;
    public function __construct( TransactionRepository $repository ) {
        $this->model =  Transaction::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = TransactionRequest::class;
        $this->updateRequest    = UpdateRequest::class;
    }

    public function isPublicRoute( string $routeName ): Bool {
        return true;
    }

    
}
