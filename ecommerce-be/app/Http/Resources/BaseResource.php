<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaseResource extends JsonResource
{
    public function toArray($request)
    {
        $request = app()->make('request');
        
        if($this->resource instanceof LengthAwarePaginator){
            return $this->getPaginate();
        }
        if($this->resource instanceof Collection){
            if($request->only){
                $explode = explode(',', $request->only);
                return $this->resource->map( fn($v) => $v->only($explode) );
            }
           
        }
        return parent::toArray($request);
        
    }

    public function getTo(){
        $to = $this->perPage() * $this->currentPage();
        if ( $to >= $this->total() ){
            return $this->total();
        }
        return $to;
    }

    public function getPaginate(){

        
        $items = $this->items();

        if(method_exists($this, 'hasStoreAdvertisement')){
            $items =  $this->hasStoreAdvertisement();
        }

        if(method_exists($this, 'makeVisibleFields')){
            $items =  $this->makeVisibleFields();
        }
        

        return [
            'data' => $items,
            'meta' => [
                'per_page'      => $this->perPage(),
                'current_page'  => $this->currentPage(),
                'total'         => $this->total(),
                'last_page'     => $this->lastPage(),
                'to'            => $this->getTo(),
                'from'          => ( $this->perPage() * $this->currentPage() ) - $this->perPage() + 1
            ]
        ];
    }

    
}