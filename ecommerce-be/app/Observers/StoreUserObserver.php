<?php

namespace App\Observers;
use Illuminate\Support\Facades\Mail;
use App\Models\StoreUser;
use App\Mail\UserRegistration;
use App\Models\Store;
class StoreUserObserver
{

    public function created(StoreUser $storeUser){

        $referer = env('APP_URL');
        $store = Store::find($storeUser->store_id);
        $storeUserInfo['storeName'] = $store->name;
        $storeUserInfo['verification_code'] = $referer . '/store-users/verfication_code/' . $storeUser->verification_code;
        
        Mail::to($storeUser->email)
            ->send(new UserRegistration($storeUserInfo));
    }


}
