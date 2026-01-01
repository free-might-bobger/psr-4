<?php

namespace App\Service;

use App\Models\Item;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class PublicStoreItemService {

    use OptimusRequiredToModel;

    public function getPublicStoreItem(int $id) : Item
    {
        return Item::where('id', $this->optimus()->decode($id))
        ->with('images', 'itemPrice.unit')
        ->first();
    }


}