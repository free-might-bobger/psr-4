<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->citymunDesc;
    }
    public function getValueAttribute(){
        return $this->citymunCode;
    }
}
