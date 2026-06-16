<?php

namespace App\Repositories;

use App\Models\Status;

class StatusRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
 {
        $this->setModel( new Status() );
        $this->cacheKey = 'Statuses-get';
    }


}
