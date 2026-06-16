<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\BaseResource;
use Auth;

class StoreResource extends BaseResource
{

    public function makeVisibleFields()
    {

        return collect($this->items())->map(function ($item) {
            $item->makeVisible(['mobile']);
            return $item;
        });

    }
}
