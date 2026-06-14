<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyStoreTransaction\UpdateRequest;
use App\Models\Transaction;
use App\Http\Requests\BaseIndexRequest;
use App\Repositories\MyStoreTransactionRepository;
use App\Services\TransactionService;
use App\Http\Resources\MyTransaction\IndexResource;
use App\Http\Resources\MyTransaction\ShowResource;
use Illuminate\Http\JsonResponse;
class MyTransactionController extends ApiController {

    protected string $model;
    public function __construct( 
        MyStoreTransactionRepository $repository,
        TransactionService $transactionService ) {
        $this->model =  Transaction::class;
        $this->repository = $repository;
        $this->transactionService = $transactionService;
        $this->indexRequest = BaseIndexRequest::class;
        $this->updateRequest    = UpdateRequest::class;

    }

    public function markedAsReceived(int $transactionId): JsonResponse {
        $updatedTransaction = $this->transactionService->markedAsReceived($transactionId);
        return response()->json('You have successfully marked the transaction as received.');
    }

    public function getResource(){
        return new IndexResource($this->result);
    }

    public function showResource(){
        return new IndexResource($this->result);
    }

}
