<?php

namespace App\Http\Requests\Item;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class StoreRequest extends BaseRequest
{
    use RequestValidation, OptimusRequiredToModel;

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'required', 
            'description'   => 'required',  
            'unit_id'       => 'required',
            'store_id'      => 'required',
            'category_id'   => 'required',
            'files'         => 'sometimes'
        ];
    }

    
}
