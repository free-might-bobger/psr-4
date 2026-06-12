<?php

namespace App\Http\Resources\PublicItem;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\BaseResource;

class IndexResource extends BaseResource
{
   public function __construct(Collection $resource) {
        $this->fields = ['id', 'label', 'name', 'optimus_id', 'store.id', 'store.label', 'store.name', 'store.optimus_id', 'store.latitude', 'store.longitude'];
        parent::__construct($resource);
    }
    
}
