<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Requests\BaseIndexRequest;;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{

    use HasFactory, SoftDeletes, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'payment_method_id',
        'receive_method_id',
        'delivery_charge',
        'contact_number',
        'reference_id',
        'status_id',
        'total',
        'grand_total'
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

    public function getDeliveryChargeAttribute($value){
        return number_format( $value ,2,".",",");
    }
    // public function pickupTime(){
    //     return $this->hasMany(PickupTime::class, 'transaction_id', 'id');
    // }

    // public function getFormatDeliveryAttribute(){
    //     return number_format($this->delivery_charge,2,".",",");
    // }

    // public function getDateTimeAttribute(){

    //     $request = app(BaseIndexRequest::class)->all();
    //     $storeId = Arr::get($request, 'pickup_store_id', null);
    //     if( count($this->pickupTime) && $storeId ){
    //        $dateTime = $this->pickupTime->filter(function($value) use ($storeId) {
    //             return $value['transaction_id'] == $this->id && $value['store_id'] == $this->optimus()->decode( $storeId);
    //         })->last();
    //        if($dateTime){
    //         return $dateTime->date_time;
    //        }
    //     }
    //     return null;
    // }

    // //ref_number
    // public function getMaskReferenceAttribute(){
    //     return '*****' . substr($this->ref_number, -5);
    // }


    // public function getFormattedReceiveDateAttribute(){
    //     if($this->receive_date){
    //         return Carbon::parse($this->receive_date)->toDayDateTimeString();
    //     }
    // }
}
