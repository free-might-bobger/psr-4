<?php 

namespace App\Services;
use App\Models\StoreUser;
class StoreUserService {
    
    public function verifyActivationCode(String $verificationCode): StoreUser | null {

        $storeUser = StoreUser::whereVerificationCode($verificationCode)->first();
        if($storeUser){
            $storeUser->update([
                'verification_code' => null,
                'is_verified' => 1
            ]);
            return $storeUser;
        }
        return null;
    }

}