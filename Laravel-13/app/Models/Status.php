<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Status extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'status';

    protected $fillable = ['name'];

    protected $appends = ['label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }
}
