<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileRepository extends BaseRepository implements BaseInterface
 {

    public function __construct()
    {
        $this->setModel( new User() );
        $this->cacheKey = 'User-get';
    }

    public function profileUpdate(array $params): User {
       $user = User::find(Auth::user()->id);
       $user->update($params);
       $user->touch();
       return $user;
    }

}
