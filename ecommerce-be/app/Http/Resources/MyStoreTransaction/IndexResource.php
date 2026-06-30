<?php

namespace App\Http\Resources\MyStoreTransaction;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\BaseResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Collection|LengthAwarePaginator|Model|Builder $resource) {
        $this->fields = [];
        
        // If it's a Builder, execute it to get proper results
        if ($resource instanceof Builder) {
            $resource = $resource->get();
        }
        
        parent::__construct($resource);
    }
    
}
