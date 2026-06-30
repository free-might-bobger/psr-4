<?php

namespace App\Http\Requests\Store;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use App\Traits\Obfuscate\OptimusId;
class MobileRequest extends BaseRequest
{
    use RequestValidation, OptimusId;

    public function rules()
    {
        $id = '';
        if ($this->get('id')) {
            $id = $this->optimus()->decode($this->get('id'));
        }
        
        return [
            'mobile' => [
                    'unique:stores,mobile,'. $id,
                ]
        ];
    }

    
}
