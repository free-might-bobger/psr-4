<?php

namespace App\Http\Resources;
use Illuminate\Database\Eloquent\Collection;

class PublicStoreResource extends BaseResource
{
   public function __construct(Collection $resource) {
        $this->fields = ['label', 'name', 'latitude', 'longitude', 'optimus_id'];
        parent::__construct($resource);
    }
    
}
