<?php

namespace App\Repositories\Audit;

use App\Models\Audit;
use App\Repositories\BaseRepository;
use App\Repositories\BaseInterface;

class AuditRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->model = new Audit;
        $this->cacheKey = 'Audits-get';
    }

}
