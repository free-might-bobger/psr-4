<?php

namespace App\Repositories;

use App\Models\StoreUserMenu;
use App\Traits\Obfuscate\OptimusId;
use Illuminate\Database\Eloquent\Model;

class StoreUserMenuRepository extends BaseRepository implements BaseInterface
{
    use OptimusId;
    
    public function __construct()
    {
        $this->setModel(new StoreUserMenu());
        $this->cacheKey = 'StoreUserMenus-get';
    }

    public function create(): Model
    {
        $this->params['store_user_id'] = $this->optimus()->decode($this->params['store_user_id']);
        
        return $this->model->create($this->params);
    }
}
