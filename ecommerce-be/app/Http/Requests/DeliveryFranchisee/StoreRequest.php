<?php

namespace App\Http\Requests\DeliveryFranchisee;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Auth;
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
            'franchisee_id'                 => 'required',
            'user_id'                       => 'required',
            'refcitymun_id'                 => 'required',
            'refprovince_id'                => 'required',
            'mobile'                        => 'required'
        ];
    }

    
}
