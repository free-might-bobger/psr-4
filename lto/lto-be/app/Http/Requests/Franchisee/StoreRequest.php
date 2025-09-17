<?php

namespace App\Http\Requests\Franchisee;

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
            'mobile'    => 'sometimes',
            'name'      => 'sometimes',
            'refcitymun_id'   => 'sometimes',
            'refprovince_id' => 'sometimes',
            'user_id'     => 'sometimes',
           
        ];
    }

    
}
