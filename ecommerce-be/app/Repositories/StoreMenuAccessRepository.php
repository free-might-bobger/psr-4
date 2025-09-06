<?php

namespace App\Repositories;

use App\Models\StoreMenuAccess;

class StoreMenuAccessRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->setModel(new StoreMenuAccess());
        $this->cacheKey = 'StoreMenuAccesss-get';
    }

}
