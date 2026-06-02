<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\LabelValue;
use OwenIt\Auditing\Contracts\Auditable;

class StoreMenu extends Model implements Auditable
{
    use HasFactory, LabelValue;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_menus';
    protected $fillable = [
        'icon',
        'name'
    ];

    protected $appends = ['label', 'value'];
}
