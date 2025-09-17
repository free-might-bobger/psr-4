<?php

namespace App\Repositories\Module;

use App\Models\Module;
use App\Repositories\BaseRepository;

class ModuleRepository extends BaseRepository implements ModuleInterface
{
    
    public function __construct()
    {
        $this->model = new Module;
        $this->cacheKey = 'Modules-get';
    }

}
