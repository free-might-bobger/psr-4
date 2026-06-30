<?php

namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleInterface
{
    
    public function __construct()
    {
        $this->model = new Role;
        $this->cacheKey = 'roles-get';
    }

}
