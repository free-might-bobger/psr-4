<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Pagination\LengthAwarePaginator;
class ItemResource extends BaseResource
{
    public function __construct($result) {
        parent::__construct($result);
    }

    public function makeVisibleFields(){
        $request = app(BaseIndexRequest::class);
        return collect($this->items())->map( function($item) use ($request) {
            $explode = explode(',', $request->visibleColumns);
            if($item->itemPrice){
                $item->itemPrice->makeVisible($explode);
            }
           
            return $item;
        });

        return $this->items();
        
    }

    public function toArray($request)
    {
        
        if($this->resource instanceof LengthAwarePaginator){
            return $this->getPaginate();
        }

        // $request = app(BaseIndexRequest::class);
        // $explode = explode(',', $request->visibleColumns);
        // $this->resource->itemPrice->makeVisible($explode);

        return parent::toArray($request);
        
    }
}
