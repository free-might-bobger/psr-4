<?php

namespace App\Http\Requests\Address;

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
            'city_mun_code'     => 'sometimes',
            'province_code'     => 'sometimes',
            'brgy_code'         => 'sometimes', 
            'landmark'          => 'sometimes|string',  
            'lat'               => 'sometimes|numeric',  
            'lng'               => 'sometimes|numeric',
            'addressable_id'    => 'sometimes|integer',
            'addressable_type'  => 'sometimes|string',
            'latitude'          => 'sometimes',
            'longitude'         => 'sometimes',
            'default_address'   => 'sometimes',
            'transaction_id'    => 'sometimes'
        ];
    }

    

    
}
