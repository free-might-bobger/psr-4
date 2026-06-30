<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class PaymentOption extends Model
{
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'payment_options';
    protected $fillable = [
        'name', 'amount'
    ];
    protected $appends = ['optimus_id', 'label', 'value'];
    public function getAmountAttribute($value): string {
        return number_format($value, 2, '.', ',');
    }

    public function getLabelAttribute(){
        return $this->name . ' (' . $this->amount . ')';
    }
}
