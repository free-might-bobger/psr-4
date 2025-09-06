<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailRequest;
use App\Http\Requests\User\MobileRequest;
use App\Http\Requests\User\UserNameRequest;
use App\Http\Requests\User\IsMobileAUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserValidationController extends Controller
{
    public function userName(UserNameRequest $request)
    {

    }

    public function email(EmailRequest $request)
    {

    }

    public function mobile(MobileRequest $request)
    {

    }

    public function isMobileExist(Request $request){
        $user = User::whereMobile($request->mobile)->first();
        if($user) {
            return response()->json([
                'data' => true
            ]);
        }
        return response()->json([
            'data' => false
        ]);
    }
    
}