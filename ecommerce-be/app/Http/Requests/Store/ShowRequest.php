<?php

namespace App\Http\Requests\Store;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;

class ShowRequest extends BaseRequest
 {
    use RequestValidation;

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */

    public function rules()
 {
        return [];
    }

}
