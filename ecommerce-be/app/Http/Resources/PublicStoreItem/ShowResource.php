<?php

namespace App\Http\Resources\PublicStoreItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\BaseResource;

class ShowResource extends BaseResource
{
   public function __construct(Model|Collection $resource) {
        $this->fields = ['id', 'optimus_id', 'name', 'images', 'primary_img', 'item_price'];
        parent::__construct($resource);
    }
    
}
