<?php

namespace App\Http\Requests\Invoice;

use App\Traits\Requests\RequestValidation;
use App\Http\Requests\BaseRequest;
use App\Traits\Obfuscate\OptimusId;
use App\Repositories\StoreUserRepository;

class IndexRequest extends BaseRequest
{
    use RequestValidation, OptimusId;

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'with'       => 'sometimes',
            'filters'    => 'sometimes',
            'orderBy'    => 'sometimes'
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
        $storeIds = app(StoreUserRepository::class)->getStoreIds();

        return array_merge($currentRequests, [
            'store_ids'         => $storeIds
        ] );

    }

    
}
