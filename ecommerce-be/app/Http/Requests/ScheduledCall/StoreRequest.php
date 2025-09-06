<?php

namespace App\Http\Requests\ScheduledCall;

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
            'title'                     => 'required',
            'desc'                      => 'required',
            'date'                      => 'required',
            'security_question'         => 'required',
            'security_answer'           => 'required',
            'scheduled_call_status'     => 'sometimes'
        ];
    }

    
}
