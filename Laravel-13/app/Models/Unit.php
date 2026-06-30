<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\LabelValue;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model  implements Auditable
{
    use HasFactory, LabelValue;
    use \OwenIt\Auditing\Auditable;


    protected $table = 'units';
    protected $fillable = ['id', 'name'];

    protected $appends = ['label', 'value'];
    
}
