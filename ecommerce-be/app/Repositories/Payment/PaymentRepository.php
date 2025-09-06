<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use App\Repositories\BaseRepository;
use App\Models\Franchisee;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Support\Arr;

class PaymentRepository extends BaseRepository implements PaymentInterface
{
    use OptimusRequiredToModel;
    public function __construct()
    {
        $this->model = new Payment;
        $this->cacheKey = 'Payments-get';
        $this->modelClassName = get_class(new Payment);
    }

}
