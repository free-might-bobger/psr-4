<?php

namespace App\Http\Requests\Transaction;

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
            'payment_method_id'     => 'sometimes|integer',
            'receive_method_id'     => 'sometimes|integer',
            'items'                 => 'sometimes',
            'delivery_charge'       => 'sometimes',
            'delivery_charge_id'    => 'sometimes',
            'optimus_id'            => 'sometimes',
            'status_id'             => 'sometimes'
        ];  
    }

    
}
