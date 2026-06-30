<?php

namespace App\Repositories;

use App\Models\User;

class ProfileRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new User() );
        $this->cacheKey = 'User-get';
    }

}
