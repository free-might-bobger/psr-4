<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\ItemPrice;
use App\Enum\Status;
use Auth;

class CustomerTransactionRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new Transaction() );
        $this->cacheKey = 'Transactions-get';
    }

}
