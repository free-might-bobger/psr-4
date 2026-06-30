<?php

namespace App\Http\Resources\Profile;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\BaseResource;
use Illuminate\Database\Eloquent\Model;

class IndexResource extends BaseResource
{
   public function __construct(Model $resource) {
        $this->fields = ['name', 'mobile'];
        parent::__construct($resource);
    }
    
}
