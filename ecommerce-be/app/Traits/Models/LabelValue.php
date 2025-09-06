<?php 

namespace App\Traits\Models;

trait LabelValue
{
    
    public function getLabelAttribute(){
        return $this->name;
    }

    public function getValueAttribute(){
        return $this->id;
    }
}