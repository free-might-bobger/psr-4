<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    use HasFactory, OptimusRequiredToModel, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'orders';
   
    protected $fillable = [
        'transaction_id',
        'store_id',
        'item_id',
        'item_name',
        'item_description',
        'base_price',
        'store_price',
        'online_price',
        'unit_id',
        'qty',
        'status'
    ];

    protected $appends = ['optimus_item','subtotal', 'format_subtotal', 'format_price', 'optimus_id'];

    public function getOptimusRequiredToModeltoreAttribute(){
        return $this->optimus()->encode($this->store_id);
    }

    public function getOptimusItemAttribute(){
        return $this->optimus()->encode($this->item_id);
    }

    public function store(){
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function getSubtotalAttribute(){
        return $this->qty * $this->online_price;
    }

    public function getFormatSubtotalAttribute(){
        return number_format( $this->qty * $this->online_price,2,".",",");
    }

    public function getFormatPriceAttribute(){
        return number_format($this->online_price,2,".",",");
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }


}
