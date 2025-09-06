<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;

class ReceiveMethod extends Model implements Auditable
{
    use HasFactory, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'receive_methods';
    protected $fillable = [
        'name',
        'is_default'
    ];
    protected $hidden = ['id'];

    protected $appends = ['optimus_id', 'label', 'value'];

    public function getLabelAttribute(){
        return $this->name;
    }
    public function getValueAttribute(){
        return $this->id;
    }
}
