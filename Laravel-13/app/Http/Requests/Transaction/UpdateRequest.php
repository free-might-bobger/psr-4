<?php

namespace App\Http\Requests\Transaction;

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
            'status_id'     => 'sometimes|integer',
        ];  
    }

    
}
