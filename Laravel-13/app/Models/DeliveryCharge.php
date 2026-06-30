<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DeliveryChargeService;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Config;

class DeliveryCharge extends Model
 {
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'delivery_charges';
    protected $fillable = [
        'name',
        'base_amount'
    ];

    protected $appends = [ 'delivery_amount' ];

    public function getDeliveryAmountAttribute(): float {
        
        $request = app()->make( 'request' );
        $calculateDeliveryChargeService = app()->make( DeliveryChargeService::class );
        return $calculateDeliveryChargeService->calculateTotalDeliveryCharge( $this->base_amount, $request->storeIds);

    }

    

    

}
