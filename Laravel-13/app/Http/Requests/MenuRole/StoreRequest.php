<?php

namespace App\Http\Requests\MenuRole;

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
            'accessRights'                => 'sometimes',
            'optimus_id'                  => 'sometimes',
        ];
    }

    
}
