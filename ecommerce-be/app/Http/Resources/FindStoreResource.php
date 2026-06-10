<?php

namespace App\Http\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FindStoreResource extends BaseResource
{
   public function __construct(Collection|Model $resource) {
        $this->fields = ['id', 'label', 'name', 'latitude', 'longitude', 'optimus_id'];
        parent::__construct($resource);
    }
    
}
