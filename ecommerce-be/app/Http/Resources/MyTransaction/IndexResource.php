<?php

namespace App\Http\Resources\MyTransaction;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\BaseResource;

class IndexResource extends BaseResource
{
   public function __construct(Collection $resource) {
        $this->fields = [];
        parent::__construct($resource);
    }
    
}
