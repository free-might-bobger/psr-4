<?php

namespace App\Repositories\Province;

use App\Models\Province;
use App\Repositories\BaseRepository;

class ProvinceRepository extends BaseRepository implements ProvinceInterface
{
    
    public function __construct()
    {
        $this->model = new Province;
        $this->cacheKey = 'Provinces-get';
    }

}
