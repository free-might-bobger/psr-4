<?php

namespace App\Http\Requests\Invoice;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Auth;
class StoreRequest extends BaseRequest
{
    use RequestValidation, OptimusRequiredToModel;

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                          => 'sometimes',
            'description'                   => 'sometimes',
            'invoiceItems'                  => 'sometimes',
            'store_id'                      => 'sometimes',
        ];
    }

    public function all($keys = null): array
    {
        $all = parent::all($keys);
        $currentRequests = array_intersect_key($all, $this->rules());
        return $this->modifyRequest($currentRequests);
    }

    public function modifyRequest($currentRequests)
    {
        return array_merge($currentRequests, [
            'store_id'      => $this->optimus()->decode($currentRequests['store_id']),
            'created_by'    => Auth::User()->id
        ] );

    }
    
}
