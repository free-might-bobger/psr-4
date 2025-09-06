<?php 

namespace App\Traits\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait RequestValidation
{
    
    protected function failedValidation(Validator $validator){
        
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}