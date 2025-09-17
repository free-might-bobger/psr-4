<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentMethodRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->setModel(new PaymentMethod());
        $this->cacheKey = 'Payments-get';
    }

}
