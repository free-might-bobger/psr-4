<?php

namespace App\Observers;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\UserRegistration;

class UserObserver
{

    public function created(User $user){

        $referer = env('APP_URL');

        $userInfo = [
            'name' => $user->firstname . ' ' .  $user->lastname,
            'email' => $user->email
        ];

        $userInfo['activation_code'] = $referer . '/verify/activation_code/' . $user->activation_code;
        
        Mail::to($user->email)
            ->send(new UserRegistration($userInfo));
    }


}
