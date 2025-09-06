<?php

namespace App\Http\Requests\PickupTime;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    use RequestValidation;

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_id'      => 'sometimes',
            'store_id'            => 'sometimes',
            'date_time'           => 'sometimes'
        ];
    }

    
}
