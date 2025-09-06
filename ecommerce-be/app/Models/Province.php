<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\LabelValue;

class Province extends Model
{
    use HasFactory, LabelValue;

    protected $table = 'provinces';

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }
}
