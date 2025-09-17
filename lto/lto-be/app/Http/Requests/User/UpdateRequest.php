<?php

namespace App\Http\Requests\User;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
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
            'mobile'        => 'sometimes',
            'otp'           => 'sometimes',
            'name'          => 'sometimes',
            'default_address' => 'sometimes',
            'city_mun_code'     => 'sometimes',
            'province_code'     => 'sometimes',
            'brgy_code'         => 'sometimes',
            'optimus_id'        => 'sometimes',
            'addressable_id'    => 'sometimes',
            'latitude'          => 'sometimes',
            'longitude'         => 'sometimes',
            'landmark'          => 'sometimes',
            'address'           => 'sometimes',
            'password'          => 'sometimes',
            'selectedRoles'           => 'sometimes'
        ];
    }

    
}
