<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class InvoiceItem extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'invoice_items';
    protected $fillable = [
        'name', 'amount', 'qty', 'subtotal', 'invoice_id', 'is_approved', 'approved_by'
    ];

    public function getAmountAttribute($value){
       return number_format($value, 2, '.', ',');
    }

    public function getSubtotalAttribute($value){
        return number_format($value, 2, '.', ',');
     }
}
