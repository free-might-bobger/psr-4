<?php

namespace App\Repositories\MenuRole;

use App\Models\MenuRole;
use App\Repositories\BaseRepository;

class MenuRoleRepository extends BaseRepository implements MenuRoleInterface
{
    
    public function __construct()
    {
        $this->model = new MenuRole;
        $this->cacheKey = 'MenuRoles-get';
    }

    public function role_id($param){
        $explode = explode(',', $param);
        $this->model = $this->model->whereIn('role_id', $explode );
    }

}
