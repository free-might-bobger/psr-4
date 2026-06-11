<?php

namespace App\Http\Resources\PublicStoreItem;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\BaseResource;

class ShowResource extends BaseResource
{
   public function __construct(Collection $resource) {
        $this->fields = ['label', 'name', 'latitude', 'longitude', 'optimus_id'];
        parent::__construct($resource);
    }
    
}
