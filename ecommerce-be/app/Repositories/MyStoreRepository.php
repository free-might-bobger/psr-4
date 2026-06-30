<?php

namespace App\Repositories;

use App\Models\Store;

class MyStoreRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new Store() );
        $this->cacheKey = 'Stores-get';
    }

    /**
     * Filter stores by their active status.
     * This trigger from BaseRepository filterQuery
     * filters=id:1,name:as
     * @param int $value 1 for active stores, 0 for inactive stores
     */
    public function is_active(int $value): void {
        $this->model = $this->model->where('is_active', $value);
    }


}
