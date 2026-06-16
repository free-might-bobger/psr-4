<?php 

namespace App\Services;
use App\Models\Store;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use App\Traits\Google\Maps;
use App\Http\Requests\BaseIndexRequest;
use Illuminate\Support\Arr;

class DeliveryChargeService {

    use OptimusRequiredToModel;

    public function calculateTotalDeliveryCharge($baseAmount, $storeIds): float {
        $totalDeliveryCharge = 0;
        foreach ( explode( ',', $storeIds ) as $storeId ) {
            $totalDeliveryCharge += $this->calculateStoreDelivery( $baseAmount, $storeId );
        }
        return $totalDeliveryCharge;
    }

    public function calculateStoreDelivery($baseAmount, int $storeId): float{
        $request = app()->make( 'request' );
        $store = Store::where( 'id', $this->optimus()->decode( $storeId ) )->first();
        $distance = Maps::calculateDistance( $store->latitude, $store->longitude, $request->latitude, $request->longitude );
        return (float) ($baseAmount * $distance);
    }

}