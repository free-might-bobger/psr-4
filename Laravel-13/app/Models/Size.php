<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\Models\LabelValue;

class Size extends Model  implements Auditable
{
    use HasFactory, LabelValue;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'sizes';
    protected $fillable = [
        'name', 'desc'
    ];

    protected $appends = ['label', 'value'];
}
