<?php

namespace App\Repositories\ReceiveMethod;

use App\Models\ReceiveMethod;
use App\Repositories\BaseRepository;

class ReceiveMethodRepository extends BaseRepository implements ReceiveMethodInterface
{
    
    public function __construct()
    {
        $this->model = new ReceiveMethod;
        $this->cacheKey = 'ReceiveMethods-get';
    }

}
