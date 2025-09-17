<?php

namespace App\Http\Requests\Store;

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
            'id'        => 'sometimes',
            'name'      => 'sometimes',
            'mobile'    => 'sometimes',
            'desc'      => 'sometimes',
            'address'   => 'sometimes',
            'default_address' => 'sometimes',
            'city_mun_code'     => 'sometimes',
            'province_code'     => 'sometimes',
            'brgy_code'         => 'sometimes',
            'optimus_id'        => 'sometimes',
            'addressable_id'    => 'sometimes',
            'refcitymun_id'     => 'sometimes',
            'refprovince_id'    => 'sometimes',
            'refbrgy_id'        => 'sometimes',
            'user_id'           => 'sometimes',
            'latitude'          => 'sometimes',
            'longitude'          => 'sometimes'

        ];
    }

}
