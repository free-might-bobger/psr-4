<?php

namespace App\Repositories;

use App\Models\Delivery;

class DeliveryRepository extends BaseRepository implements BaseInterface
{

    public function __construct()
    {
        $this->setModel(new Delivery());
        $this->cacheKey = 'Delivery-get';
    }

}
