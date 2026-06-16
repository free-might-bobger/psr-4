<?php

namespace App\Http\Requests\Franchisee;

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
                    'unique:franchisees,mobile,'. $id,
                ]
        ];
    }

    
}
