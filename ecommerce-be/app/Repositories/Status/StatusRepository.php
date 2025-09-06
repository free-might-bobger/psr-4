<?php

namespace App\Repositories\Status;

use App\Models\Status;
use App\Repositories\BaseRepository;

class StatusRepository extends BaseRepository implements StatusInterface
{
    
    public function __construct()
    {
        $this->model = new Status;
        $this->cacheKey = 'Statuss-get';
    }

}
