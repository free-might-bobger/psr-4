<?php

namespace App\Http\Requests\Store;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
class PatchRequest extends BaseRequest
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
            'desc'          => 'required|string',
            'latitude'      => 'required',
            'longitude'     => 'required',
            'mobile'        => 'required|string',
            'name'          => 'sometimes|string',
            'is_active'     => 'sometimes|boolean'

        ];
    }

}
