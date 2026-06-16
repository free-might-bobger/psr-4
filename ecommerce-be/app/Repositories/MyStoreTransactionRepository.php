<?php

namespace App\Repositories;

use App\Models\Transaction;

class MyStoreTransactionRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
 {
        $this->setModel( new Transaction() );
        $this->cacheKey = 'MyStoreTransactions-get';
    }

    public function markedAsReceived($transactionId) {
        $transaction = $this->model::findOrFail($this->optimus()->decode($transaction));
        $transaction->status_id = 5; // Assuming 5 is the ID for "Received" status
        $transaction->save();
        return $transaction;


}
