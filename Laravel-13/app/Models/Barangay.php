<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'refbrgy';

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->brgyDesc;
    }

    public function getValueAttribute(){
        return $this->brgyCode;
    }
}
