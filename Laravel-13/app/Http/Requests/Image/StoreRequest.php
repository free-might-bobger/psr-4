<?php

namespace App\Http\Requests\Image;
use App\Traits\Requests\RequestValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
           'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
