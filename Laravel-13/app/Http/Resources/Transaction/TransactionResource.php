<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\BaseResource;
use Illuminate\Pagination\LengthAwarePaginator;
class TransactionResource extends BaseResource
{
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function toArray($request)
    {
        
        if($this->resource instanceof LengthAwarePaginator){
            return $this->getPaginate();
        }
        
        $this->resource = $this->resource->setHidden([]);
        return parent::toArray($request);
        
    }

    public function makeVisibleFields(){

        if(\Auth::check()){
            $array = \Auth::User()->roles->pluck('name')->values()->all();
            if(array_intersect( ['Super Admin'], $array) ){
                return collect($this->items())->map( function($item){
                    $item->makeVisible(['ref_number', 'complete_address', 'contact_number']);
                    return $item;
                });
            }
        }

        return $this->items();
        
    }

    public function inArray($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->inArray($needle, $item, $strict))) {
                return true;
            }
        }
    
        return false;
    }
}
