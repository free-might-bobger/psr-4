<?php

namespace App\Repositories;

use App\Models\RoleUser;
use Illuminate\Database\Eloquent\Model;

class RoleUserRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new RoleUser() );
        $this->cacheKey = 'RoleUser-get';
    }

    public function create(): Model
    {
        return $this->model->create($this->params);
    }

}
