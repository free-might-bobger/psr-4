<?php

namespace App\Http\Requests\CloudToken;

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
            'token'             => 'required|string',
            'tokenable_id'      => 'required|integer',
            'tokenable_type'    => 'required'
        ];
    }

    
}
