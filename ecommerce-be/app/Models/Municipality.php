<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\LabelValue;

class Municipality extends Model 
{
    use HasFactory, LabelValue;

    protected $table = 'cities';

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }
}
