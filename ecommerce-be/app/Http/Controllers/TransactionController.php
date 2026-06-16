<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\Transaction\UpdateRequest;
use App\Http\Resources\CustomerTransaction\IndexResource;

class TransactionController extends ApiController {

    protected string $model;
    public function __construct( TransactionRepository $repository ) {
        $this->model =  Transaction::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->storeRequest    = TransactionRequest::class;
        $this->updateRequest    = UpdateRequest::class;
    }

   public function getResource(): IndexResource {
    return new IndexResource($this->result);
   }

   public function showResource(): IndexResource {
    return new IndexResource($this->result);
   }

    
}
