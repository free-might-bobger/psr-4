<?php

namespace App\Http\Requests\DeliveryFranchisee;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class MobileRequest extends BaseRequest
{
    use RequestValidation, OptimusRequiredToModel;

    public function rules()
    {
        $id = '';
        if ($this->get('id')) {
            $id = $this->optimus()->decode($this->get('id'));
        }
        
        return [
            'mobile' => [
                    'unique:delivery_franchisee,mobile,'. $id,
                ]
        ];
    }

    
}
