<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
class ItemResource extends BaseResource
{
    public function __construct($result) {
        parent::__construct($result);
    }

    public function toArray($request)
    {
        
        return parent::toArray($request);
    }
}
