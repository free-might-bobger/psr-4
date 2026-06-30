<?php

namespace App\Repositories;

use App\Models\StoreUser;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class StoreUserRepository extends BaseRepository
{
    
    public function __construct()
    {
        $this->getModel();
        $this->cacheKey = 'StoreMenus-get';
    }

    public function getModel(): Model {
        $this->model = new StoreUser;
        return $this->model;
    }

}
