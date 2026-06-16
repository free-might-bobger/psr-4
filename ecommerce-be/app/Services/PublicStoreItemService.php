<?php

namespace App\Services;

use App\Models\Item;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class PublicStoreItemService {

    use OptimusRequiredToModel;

    public function getPublicStoreItem(int $id) : Item
    {
        /* April 3, 2026 TODO Remove hidden original and selling price */
        return Item::where('id', $this->optimus()->decode($id))
        ->with('images', 'itemPrice.unit')
        ->first();
    }


}