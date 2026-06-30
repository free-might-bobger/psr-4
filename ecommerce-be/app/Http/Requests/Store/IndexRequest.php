<?php

namespace App\Http\Requests\Store;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
class IndexRequest extends BaseRequest
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
            'orderBy'          => 'required|string',
            'columns'          => 'required|string',
            'page'             => 'required|integer',
            'limit'            => 'required|integer',
            'deleted'          => 'sometimes|integer'

        ];
    }

}
