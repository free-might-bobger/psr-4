<?php

namespace App\Repositories\PaymentOption;

use App\Models\PaymentOption;
use App\Repositories\BaseRepository;
use App\Models\Franchisee;
use App\Traits\Obfuscate\OptimusId;
use Illuminate\Support\Arr;

class PaymentOptionRepository extends BaseRepository implements PaymentOptionInterface
{
    use OptimusId;
    public function __construct()
    {
        $this->model = new PaymentOption;
        $this->cacheKey = 'PaymentOptions-get';
        $this->modelClassName = get_class(new PaymentOption);
    }

}
