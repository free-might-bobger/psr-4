<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;

class TransactionController extends ApiController {
    public function __construct( TransactionRepository $repository ) {
        $this->model =  Transaction::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = TransactionRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute( string $routeName ): Bool {
        return true;
    }

    public function store() {
        
        $request = app($this->storeRequest);
        $this->result = $this->repository
            ->createTransaction( 
                $request->items, 
                $request->deliveryCharge, 
                $request->selectedReceiveMethod, $request->selectedPaymenthMethod, 
                $request->lat, 
                $request->lng,
                $request->total
             );

        return $this->getResource();
    }
}
