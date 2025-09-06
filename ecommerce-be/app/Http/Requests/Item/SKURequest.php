<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\Obfuscate\OptimusRequiredToModel;
class SKURequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {

        return [
            'sku' => Rule::unique('items')->where(function ($query) {
                return $query
                    ->where('sku', $this->get('sku') )
                    ->when(
                        $this->get('id'),
                        function($query){
                            return $query->whereNotIn('id', [ $this->optimus()->decode( $this->get('id') ) ]  );
                        }
                    )
                    ->where('store_id',  $this->optimus()->decode( $this->get('store_id') ) );
            })
        ];
    }
}
