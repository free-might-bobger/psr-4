<?php

namespace App\Repositories\Menu;

use App\Models\Menu;
use App\Repositories\BaseRepository;

class MenuRepository extends BaseRepository implements MenuInterface
{
    
    public function __construct()
    {
        $this->model = new Menu;
        $this->cacheKey = 'menus-get';
    }

    public function is_visible($value){
        $this->model->where('is_visible', $value);
    }

}
