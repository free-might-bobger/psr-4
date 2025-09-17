<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use App\Models\SellingPrice;
use OwenIt\Auditing\Contracts\Auditable;

class ItemPrice extends Model implements Auditable
{
    use HasFactory, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;
    /**
     * price = base_price
     * selling_price = store_price
     * online_price = online_price
     */
    protected $table = 'item_prices';
    protected $fillable = [
        'selling_price', 'online_price', 'category_id', 'color_id', 'size_id', 'item_id', 'original_price', 'unit_id', 'delivery_charge_id', 'qty'
    ];
    protected $appends = ['optimus_id', 'total_qty'];

    protected $casts = [
        'original_price'         => 'float'
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }


    public function deliveryCharge(){
        return $this->belongsTo(DeliveryCharge::class);
    }

    public function scopeItemSearch($q, $string){
        $q->where('id', 1);
    }

    public function getSellingPriceAttribute($value){

        if($value){
            return floatval($value);
        }
        
        $sellingPrice = SellingPrice::where('store_id',  $this->item->store_id)
                        ->where('is_default', 1)
                        ->first();
        
        if($sellingPrice){
            $sellingPrice = $this->price + ($this->price * $sellingPrice->percentage);
            return floatVal( number_format(ceil($sellingPrice),2, '.', ',') );
        }

        return $sellingPrice;
    }

    public function getOnlinePriceAttribute($value){
        if($value){
            return floatval($value);
        }
        $onlinePrice = OnlinePrice::where('is_default', 1)->first();
        if($onlinePrice){
            $onlinePrice =  $this->selling_price + ($this->selling_price * $onlinePrice->percentage);
            return floatVal( number_format(ceil($onlinePrice),2, '.', ',') );
        }
    }

    public function getTotalQtyAttribute(){
        // if($this->itemQuantity->count()  > 0){
        //     return $this->itemQuantity->sum( fn ($v) => $v['qty']);
        // }
        return '';
        
    }

    public function unit(){
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    public function color(){
        return $this->hasOne(Color::class, 'id', 'color_id');
    }

    public function size(){
        return $this->hasOne(Size::class, 'id', 'size_id');
    }




    
}
