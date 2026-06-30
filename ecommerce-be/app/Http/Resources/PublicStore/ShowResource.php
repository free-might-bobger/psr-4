<?php

namespace App\Http\Resources\PublicStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\BaseResource;

class ShowResource extends BaseResource
{
   public function __construct(Model|LengthAwarePaginator $resource) {
        $this->fields = ['id', 'optimus_id', 'name', 'label'];
        parent::__construct($resource);
    }
    
}
