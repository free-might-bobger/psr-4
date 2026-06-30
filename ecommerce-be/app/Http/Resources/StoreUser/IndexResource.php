<?php

namespace App\Http\Resources\StoreUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = ['optimus_id', 'store.name', 'store.mobile', 'store.optimus_id', 'email', 'is_verified', 'user.optimus_id', 'user.email', 'storeUserMenu'];
        parent::__construct($resource);
    }
    
}
