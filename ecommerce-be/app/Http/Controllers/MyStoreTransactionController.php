<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyStoreTransaction\UpdateRequest;
use App\Models\Transaction;
use App\Http\Requests\BaseIndexRequest;
use App\Repositories\MyStoreTransactionRepository;
use App\Http\Resources\MyStoreTransaction\IndexResource;
use App\Http\Resources\MyStoreTransaction\ShowResource;

class MyStoreTransactionController extends ApiController {

    protected string $model;
    public function __construct( MyStoreTransactionRepository $repository ) {
        $this->model =  Transaction::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->updateRequest    = UpdateRequest::class;

    }

    public function markedAsReceived(int $transactionId) {
        $updatedTransaction = $this->repository->markedAsReceived($transactionId);
        return response()->json($updatedTransaction);
    }

      public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new ShowResource($this->result);
    }

}