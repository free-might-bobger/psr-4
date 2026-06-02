<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class StoreMenuAccess extends Model implements Auditable
 {
    use HasFactory, SoftDeletes, OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_menu_access';
    protected $fillable = [
        'store_menu_id', 'access_right_id', 'user_id', 'store_id'
    ];

    protected $appends = [ 'optimus_id' ];

    public function storeMenu() {
        return $this->belongsTo( StoreMenu::class );
    }

    public function accessRight() {
        return $this->belongsTo( AccessRight::class );
    }

    public function user() {
        return $this->belongsTo( User::class );
    }

    public function store() {
        return $this->belongsTo( Store::class );
    }

}
