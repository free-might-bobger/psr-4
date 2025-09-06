<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StoreUserMenu extends Model  implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'store_user_menu';

    protected $fillable = [
        'store_user_id',
        'store_menu_id'
    ];

    public function storeMenu(){
        return $this->hasOne(StoreMenu::class, 'id', 'store_menu_id');
    }
}
