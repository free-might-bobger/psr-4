<?php

namespace App\Http\Requests\StoreAdvertisement;

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
            'city_id'              => 'sometimes',
            'franchisee_id'        => 'sometimes',
            'rank'                 => 'sometimes',
            'store_id'             => 'sometimes',
            'start_at'             => 'sometimes',
            'end_at'               => 'sometimes',
            'amount'               => 'sometimes',
        ];
    }

    
}
