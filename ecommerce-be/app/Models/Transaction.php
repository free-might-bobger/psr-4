<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use App\Traits\Google\Maps;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model implements Auditable
{

    use HasFactory, SoftDeletes, OptimusId;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'transactions';
    protected $fillable = [
        'store_id',
        'user_id',
        'payment_method_id',
        'receive_method_id',
        'lat',
        'lng',
        'delivery_charge',
        'contact_number',
        'note',
        'reference_id',
        'status_id',
        'total',
        'grand_total',
        'receivers_mobile',
    ];

    protected $appends = ['optimus_id'];


    public function paymentMethod(){
        return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id');
    }

    public function receiveMethod(){
        return $this->hasOne(ReceiveMethod::class, 'id', 'receive_method_id');
    }

    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->toDayDateTimeString();
    }

    public function orders(){
        return $this->hasMany(Order::class, 'transaction_id', 'id');
    }

    public function store(){
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function getDeliveryChargeAttribute($value){
        return number_format( $value ,2,".",",");
    }

    public function scopeWithinKm(Builder $query, string $latitude, string $longitude, string $radius ): void {

        $boundingBox = Maps::getBoundingBox($latitude, $longitude, $radius);

        $minLat = $boundingBox['minLat'];
        $maxLat = $boundingBox['maxLat'];
        $minLon = $boundingBox['minLon'];
        $maxLon = $boundingBox['maxLon'];

        $earthRaidusInKm = 6371;

        $query->select('transactions.*', DB::raw("
            ($earthRaidusInKm * acos(cos(radians($latitude)) 
            * cos(radians(stores.latitude)) 
            * cos(radians(stores.longitude) - radians($longitude)) 
            + sin(radians($latitude)) 
            * sin(radians(stores.latitude)))) AS distance
        "))
        ->join('stores', 'transactions.store_id', '=', 'stores.id')
        ->whereBetween('stores.latitude', [$minLat, $maxLat])
        ->whereBetween('stores.longitude', [$minLon, $maxLon])
        ->having('distance', '<=', $radius)
        ->orderBy('distance', 'asc');
    }

    public function getDistanceAttribute(): float
    {

        $request = request();

        if ($request->latitude && $request->longitude) {

            $distance = Maps::calculateDistance(
                $this->store->latitude,
                $this->store->longitude,
                $request->latitude,
                $request->longitude
            );

            return (float) str_replace(',', '', $distance);
        }
        //default earth distance in km
        return 13716.96;
    }
}
