<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\DataAccessObjectContract;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, OptimusRequiredToModel;
    
    protected $table = 'invoices';
    protected $fillable = [
        'store_id', 'name', 'description', 'created_by', 'updated_by', 'is_approved', 'approved_by', 'notes'
    ];
    protected $appends = ['optimus_id'];

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->toDayDateTimeString();
    }

    public function invoiceItems(){
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }
}
