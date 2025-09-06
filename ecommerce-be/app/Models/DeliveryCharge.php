<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\BaseIndexRequest;
use App\Services\DeliveryChargeService;
use Illuminate\Support\Arr;
use App\Traits\Google\Maps;

use Config;

class DeliveryCharge extends Model
 {
    use HasFactory;

    protected $table = 'delivery_charges';
    protected $fillable = [
        'name',
        'base_amount'
    ];

    protected $appends = [ 'delivery_amount' ];

    public function getDeliveryAmountAttribute(): int {
        $request = app()->make( 'request' );

        $stores = [];
        foreach ( explode( ',', $request->storeIds ) as $storeId ) {
            $store = Store::where( 'id', $storeId )->first();
            $distance = Maps::calculateDistance( $store->latitude, $store->longitude, $request->latitude, $request->longitude );
            $stores[] = [ $storeId => $store->distance ];
        }

        $currentDistance = 0;
        $storeDistance = 0;
        foreach ( $stores as $key => $store ) {
            $storeDistance  = current( $store );
            if ( $storeDistance > $currentDistance ) {
                $currentDistance = $storeDistance;
            }
        }

        //minus one because we need to get the greatest distance and remove it to the count.
        $deliveryPickupPerStore = ( count( $stores ) - 1 ) * $this->per_store_pick_up_amount;
        return (int) (($this->base_amount * $currentDistance) + $deliveryPickupPerStore);
    }

    public function storeIds()
 {

        $request = app( BaseIndexRequest::class )->all();
        $storeIds = Arr::get( $request, 'store_ids', null );
        if ( $storeIds ) {
            return explode( ',', $storeIds );
        }
        return [];
    }

}
