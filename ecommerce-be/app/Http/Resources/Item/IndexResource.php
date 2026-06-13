<?php

namespace App\Http\Resources\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = [];
        parent::__construct($resource);
    }
    
}
