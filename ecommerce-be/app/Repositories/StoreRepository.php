<?php

namespace App\Repositories;

use App\Models\Store;

class StoreRepository extends BaseRepository implements BaseInterface
{
    public function __construct()
    {
        $this->model = new Store;
        $this->cacheKey = 'stores-get';
        $this->modelClassName = get_class(new Store);
    }

    public function getWithinKm(){

        $this->model = $this->model->withinKm($this->params['latitude'], $this->params['longitude'], $this->params['radius']);
        return $this->model->get();
    }
}
