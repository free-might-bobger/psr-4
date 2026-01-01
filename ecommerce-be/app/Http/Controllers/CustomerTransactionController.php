<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerTransactionRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CustomerTransactionController extends ApiController
{
    protected $model;
    public function __construct( CustomerTransactionRepository $repository ) {
        $this->model            =  Transaction::class;
        $this->repository       = $repository;
        $this->indexRequest     = BaseIndexRequest::class;
        $this->storeRequest     = TransactionRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute( string $routeName ): Bool {
        return true;
    }

    public function mergeRequest(): void {
        /**
         * result is limited to this user_id only.
         */
        $this->params['user_id'] = Auth::user()->id; 
    }
}
