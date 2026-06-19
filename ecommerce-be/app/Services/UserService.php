<?php

namespace App\Services;
use App\Models\StoreUser;
use App\Repositories\User\UserRepository;
use App\Mail\EmailInvitation;
use App\Mail\PasswordRecovery;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class UserService {

    public UserRepository $repository;

    public function __construct(UserRepository $repository){
        $this->repository = $repository;
    }
    public function changePassword(int $id, string $password): void {

        $user = $this->repository->findOrFail($id);
        $user->password = Hash::make($password);
        $user->update();

    }

    public function inviteByEmail(int $storeId, string $email): void {
        
        $store = $this->repository->findOrFail($storeId);
        $stringRandom = str_random(32);
        $url = env('APP_URL');
        $inviteInfo = [
            'storeName' => $store->name,
            'activation_code' => $url . '//invitation-code/' . $stringRandom
        ];
        
        Mail::to($email)
            ->send(new EmailInvitation($inviteInfo));
    }


    public function forgotPassword(string $email): void{
        $this->repository->whereEmail($email);
        $user = $this->repository->first();
        if(!$user){
            throw new \Exception('User not found');
        }

        $token = str_random(32);
        $url = env('APP_URL');
        $userInfo = [
            'name'            => $user->name,
            'email'           => $user->email,
            'activation_code' => $url . '/reset-password/' . $token,
        ];

        Mail::to($user->email)
            ->send(new PasswordRecovery($userInfo));
    }

}