<?php

namespace App\Http\Resources\PublicItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\BaseResource;

class IndexResource extends BaseResource
{
   public function __construct(LengthAwarePaginator|Model|Collection $resource) {
        $this->fields = ['id', 'label', 'name', 'optimus_id', 'distance', 'store.id', 'store.label', 'store.name', 'store.optimus_id', 'store.latitude', 'store.longitude'];
        parent::__construct($resource);
    }
    
}
