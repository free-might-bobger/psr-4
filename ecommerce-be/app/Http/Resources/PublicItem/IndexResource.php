<?php

namespace App\Http\Resources\PublicItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Collection|Model|LengthAwarePaginator $resource) {
        $this->fields = ['id', 'label', 'name', 'optimus_id', 'store.id', 'store.label', 'store.name', 'store.optimus_id', 'store.latitude', 'store.longitude'];
        parent::__construct($resource);
    }
    
}
