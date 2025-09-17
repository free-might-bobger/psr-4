<?php

namespace App\Repositories\DashboardMenu;

use App\Models\DashboardMenu;
use App\Repositories\BaseRepository;

class DashboardMenuRepository extends BaseRepository implements DashboardMenuInterface
{
    
    public function __construct()
    {
        $this->model = new DashboardMenu;
        $this->cacheKey = 'dashboard-menu-get';
    }

}
