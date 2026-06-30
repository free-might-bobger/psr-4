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

    
}
