<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'genders';
    protected $fillable = [
        'name'
    ];

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }

    public function getValueAttribute(){
        return $this->id;
    }
}
