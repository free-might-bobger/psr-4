<?php

namespace App\Http\Resources\PublicStoreItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;

class IndexResource extends BaseResource
{
   public function __construct(Model|Collection|LengthAwarePaginator $resource) {
        $this->fields = ['id', 'optimus_id', 'name', 'images', 'primary_img', 'item_price'];
        parent::__construct($resource);
    }
    
}
