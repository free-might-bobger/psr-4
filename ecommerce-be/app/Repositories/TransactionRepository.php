<?php

namespace App\Repositories;

use App\Models\Transaction;
class TransactionRepository extends BaseRepository implements BaseInterface
{

    public function __construct()
    {
        $this->setModel(new Transaction());
        $this->cacheKey = 'Transactions-get';
    }

    

}
