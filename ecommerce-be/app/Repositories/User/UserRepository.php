<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;

use App\Models\Store;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User;
        $this->cacheKey = 'users-get';
    }

    public function email($value):void
    {
        $this->model = $this->model->where('email', 'LIKE', '%' . $value . '%');
    }

    public function mobile($value):void
    {
        $this->model = $this->model->where('mobile', 'LIKE', '%' . $value . '%');
    }

    public function lastname($value):void
    {
        $this->model = $this->model->where('lastname', 'LIKE', '%' . $value . '%');
    }

    public function whereEmail($email):void {
        $this->model = $this->model->whereEmail($email);
    }

}
