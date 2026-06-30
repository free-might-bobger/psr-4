<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use App\Mail\EmailInvitation;
use App\Models\Store;

class UserRepository extends BaseRepository implements UserInterface
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

    public function inviteByEmail(int $storeId, string $email): void {
        
        $store = Store::find($storeId);
        $stringRandom = str_random(32);
        $url = env('APP_URL');
        $inviteInfo = [
            'storeName' => $store->name,
            'activation_code' => $url . '//invitation-code/' . $stringRandom
        ];
        
        Mail::to($email)
            ->send(new EmailInvitation($inviteInfo));
    }

}
