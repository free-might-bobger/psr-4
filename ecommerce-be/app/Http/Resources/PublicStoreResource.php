<?php

namespace App\Http\Resources;
use App\Models\StoreAdvertisement;
use App\Models\City;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Support\Arr;
class PublicStoreResource extends BaseResource
{
   
    public function hasStoreAdvertisement(){
        $request = app(BaseIndexRequest::class);
        
        $relationships = $this->pregSplit('@,@', $request->relation, []);

        foreach($relationships as $relation){
            if(str_contains($relation, 'city_mun_code')){
                $cityMunCode = $this->pregSplit('@;@', $relation, []);
                $lastCityMunCode =  collect($cityMunCode)->last();
                $city = City::where('citymunCode', $lastCityMunCode)->first();

                $storeAdvertisements = StoreAdvertisement::where('city_id', $city->id)->with(['store.images', 'store.address'])->orderBy('rank', 'asc')->get();

                return array_merge($storeAdvertisements->map( fn ($v) => $v->store)->values()->all(), $this->items() ) ;
            }
        }
        return $this->items();
    }

    public function pregSplit(string $pattern, $value)
    {
        return preg_split($pattern, $value, 0, PREG_SPLIT_NO_EMPTY);
    }
}
