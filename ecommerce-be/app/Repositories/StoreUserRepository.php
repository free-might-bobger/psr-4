<?php

namespace App\Repositories;

use App\Models\StoreUser;
use App\Repositories\BaseRepository;
use App\Repositories\DataAccessObjectContract;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class StoreUserRepository extends BaseRepository implements DataAccessObjectContract
{
    
    public function __construct()
    {
        $this->getModel();
        $this->cacheKey = 'StoreMenus-get';
    }

    public function getModel(): DataAccessObjectContract {
        $this->model = new StoreUser;
        return $this->model;
    }

    public function getStoreIds(): array {
        $storeUser = $this->model->where('user_id', Auth::User()->id )->get();
        return $storeUser->pluck('store_id')->values()->all();
    }

}
