<?php

namespace App\Repositories;

use App\Models\ItemPrice;
use Illuminate\Support\Arr;

class ItemPriceRepository extends BaseRepository implements BaseInterface
{
    
    public function __construct()
    {
        $this->setModel(new ItemPrice());
        $this->cacheKey = 'ItemPrices-get';
    }

    public function store_ids($param){
        $this->model = $this->model->whereIn('store_id', $param);
    }

    public function create(){
        $itemPrices = ItemPrice::where('item_id', $this->params['item_id'])->get();
        foreach($itemPrices as $itemPrice){
            $itemPrice->delete();
        }

        foreach($this->params['item_prices'] as $itemPrice ){
            ItemPrice::create([
                'item_id'   => Arr::get($this->params, 'item_id'),
                'color_id'  => Arr::get($itemPrice, 'color_id'),
                'size_id'  =>  Arr::get($itemPrice, 'size_id'),
                'unit_id'  =>  Arr::get($itemPrice, 'unit_id'),
                'original_price' => Arr::get($itemPrice, 'original_price'),
                'selling_price' => Arr::get($itemPrice, 'selling_price'),
                'online_price'  => Arr::get($itemPrice, 'online_price'),
                'qty'           => $itemPrice['qty']
            ]);
        }
    }

}
