<?php

namespace App\Repositories;

use App\Models\DeliveryCharge;
use App\Repositories\BaseRepository;

class DeliveryChargeRepository extends BaseRepository
{
    
    public function __construct()
    {
        $this->model = new DeliveryCharge;
        $this->cacheKey = 'DeliveryCharges-get';
    }

}
