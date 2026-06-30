<?php

namespace App\Repositories;

use App\Models\Store;

class SharedStoreAccessRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new Store() );
        $this->cacheKey = 'Stores-get';
    }

}
