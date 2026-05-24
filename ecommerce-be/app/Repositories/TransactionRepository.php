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

    public function getWithinKm():void{
        $this->model = $this->model->withinKm($this->params['latitude'], $this->params['longitude'], $this->params['radius']);
    }
}
