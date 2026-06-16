<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use OwenIt\Auditing\Contracts\Auditable;

class MenuRoleAccessRight extends Pivot implements Auditable
{
    use OptimusRequiredToModel;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'menu_role_access_right';
    // protected $appends = ['role'];
    // public function getRoleAttribute() {
    //     return $this->access_right_id;

    // }
//     public function setRoleAttribute($value) {
//         // your code to setter here
//     }
}
