<?php

namespace App\Service;

use App\Models\User;
use Hash;

class MobileActivationCodeService {

    public function createNewActivationcode( string $mobile ): string {
        $user = User::whereMobile( $mobile )->first();
        //get activationCode from twilio
        $passCode = 1234;
        if ( !$user ){
            User::create([
                'mobile'    => $mobile,
                'passcode'  => $passCode,
                'status'    => 1,
                'password'  => Hash::make($passCode)
            ]);

            return $passCode;
        }

        $user->update( [
            'passcode' => $passCode,
            'status'   => 1, 
            'password' => Hash::make($passCode)
        ] );

        return $passCode;

    }

    public function useOldPasscode( string $mobile ): string {

    }

    public function verifyPasscode(string $mobile, string $passCode ): Bool{
        $user = User::whereMobile( $mobile )->wherePasscode( $passCode)->first();
        if(!$user){
            return false;
        }
        return true;
    }
}