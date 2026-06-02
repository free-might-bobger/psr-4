<?php

namespace App\Http\Requests\PublicItem;
use App\Http\Requests\BaseRequest;

class IndexRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filters'          => 'string',
            'orderBy'          => 'string',
            'latitude'         => 'string',
            'longitude'        => 'string',
            'radius'           => 'string',
            'type'             => 'string',
            'limit'            => 'integer',
            'with'             => 'string',
        ];
    }
}
