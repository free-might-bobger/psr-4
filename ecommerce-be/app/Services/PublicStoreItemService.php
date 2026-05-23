<?php

namespace App\Services;

use App\Models\Item;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class PublicStoreItemService {

    use OptimusRequiredToModel;

    public function getPublicStoreItem(int $id) : Item
    {
        $item = Item::where('id', $this->optimus()->decode($id))
            ->with('images', 'itemPrice.unit')
            ->first();

        if ($item->itemPrice) {
            $item->itemPrice->makeHidden(['original_price', 'selling_price']);
        }

        return $item;
    }


}