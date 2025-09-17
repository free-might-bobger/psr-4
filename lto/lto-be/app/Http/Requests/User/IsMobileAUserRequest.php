<?php

namespace App\Http\Requests\User;

use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RequestCountRule;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
class IsMobileAUserRequest extends FormRequest
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
        
        return [
            'mobile' => [
                    Rule::exists('users')->where(function (Builder $query) {
                        return $query->where('mobile', $this->get('mobile'));
                    }),
                    new RequestCountRule( $this->all(), $this->url() )
                ]
        ];
    }

}
