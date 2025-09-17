<?php

namespace App\Repositories\AccessRight;

use App\Models\AccessRight;
use App\Repositories\BaseRepository;
use App\Repositories\BaseInterface;

class AccessRightRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->model = new AccessRight;
        $this->cacheKey = 'AccessRights-get';
    }

}
