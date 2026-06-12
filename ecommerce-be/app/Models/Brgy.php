<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusId;
use App\Traits\Models\LabelValue;
class Brgy extends Model
{
    use HasFactory, OptimusId, LabelValue;

    protected $table = 'refbrgy';
    protected $appends = ['optimus_id', 'label', 'value'];

    public function getLabelAttribute(){
        return $this->brgyDesc;
    }

    public function getValueAttribute(){
        return $this->brgyCode;
    }

}
