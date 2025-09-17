<?php

namespace App\Observers;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\RoleUser;
use App\Mail\UserRegistration;
use App\Mail\PasswordRecovery;
use App\Enum\Role;

class UserObserver
{
    public function updated(User $user){

        
    }

    public function created(User $user){
        
        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => ROLE::CUSTOMER
        ]);

        $stringRandom = str_random(32);
        $referer = env('APP_URL');

        $user->activation_code = $stringRandom;
        $user->roles()->attach([4]);
        $user->saveQuietly();

        $userInfo = [
            'name' => $user->firstname . ' ' .  $user->lastname,
            'email' => $user->email
        ];

        $userInfo['activation_code'] = $referer . 'activation_code/' . $stringRandom;
        
        Mail::to($user->email)
            ->send(new UserRegistration($userInfo));
    }


}
