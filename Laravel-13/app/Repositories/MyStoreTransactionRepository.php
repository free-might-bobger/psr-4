<?php

namespace App\Repositories;

use App\Models\Transaction;

class MyStoreTransactionRepository extends BaseRepository implements BaseInterface
{

    public function __construct()
    {
        $this->setModel(new Transaction());
        $this->cacheKey = 'MyStoreTransactions-get';
    }

    public function markedAsReceived(int $transactionId)
    {
        $transaction = $this->findOrFail($transactionId);
        $transaction->status_id = 5; // Assuming 5 is the ID for "Received" status
        $transaction->save();
        return $transaction;
    }
}
