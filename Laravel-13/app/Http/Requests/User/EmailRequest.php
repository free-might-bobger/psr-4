<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class EmailRequest extends FormRequest
{
    use OptimusRequiredToModel;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id='';
        if( $this->get('id') ){
            $id = $this->optimus()->decode( $this->get('id') );
        }
        
        return [
            'email' => 'email|unique:users,email,'. $id
        ];
    }
}
