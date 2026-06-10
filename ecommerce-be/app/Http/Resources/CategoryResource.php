<?php

namespace App\Http\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends BaseResource
{
   public function __construct(Collection|Model $resource) {
        $this->fields = ['id', 'label', 'name'];
        parent::__construct($resource);
    }
    
}
