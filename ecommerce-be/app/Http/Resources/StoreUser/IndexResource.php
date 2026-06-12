<?php

namespace App\Http\Resources\StoreUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = ['id', 'optimus_id', 'store.id', 'store.name', 'store.mobile', 'store.optimus_id'];
        parent::__construct($resource);
    }
    
}
