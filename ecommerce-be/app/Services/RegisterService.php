<?php 

namespace App\Services;
use App\Models\User;

class RegisterService {
    
public function verifyActivationCode(String $activationCode): Bool {

        $user = User::whereActivationCode( $activationCode)->first();
        if($user){
            $user->update([
                'activation_code' => null,
                'status' => 1
            ]);
            return true;
        }
        return false;
    }
}