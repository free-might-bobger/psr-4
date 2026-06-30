<?php

namespace App\Http\Requests\StoreUserMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
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
            'store_user_id' => 'required|integer',
            'store_menu_id' => [
                'required',
                'integer',
                Rule::unique('store_user_menus')->where(function ($query) {
                    return $query->where('store_user_id', $this->store_user_id);
                }),
            ],
        ];
    }
}
