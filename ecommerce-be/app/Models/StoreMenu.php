<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\Obfuscate\OptimusId;
class StoreMenu extends Model implements Auditable
{
    use HasFactory,OptimusId;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_menus';
    protected $fillable = [
        'icon',
        'name'
    ];

    protected $appends = ['label', 'value', 'optimus_id'];
}
