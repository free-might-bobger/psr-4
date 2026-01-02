<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use App\Models\User;
use App\Observers\UserObserver;
use App\Service\UserMenuService;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'     => 'required',
            'lastname'      => 'required',
            'email'         => 'required|email',
            'password'      => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        /** @var User $user */
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(UserRequest $request)
    {
        $user = User::where('mobile', $request->mobile)->first();

        if(!$user->status){
            return $this->sendError('Please verify your number.');
        }

        if(Auth::attempt(['mobile' => $request->mobile, 'password' => $request->password, 'status' => 1]))
        {
            $userMenuService = app(UserMenuService::class);
            /** @var User $user */
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['optimus_id'] =  $user->optimus_id;
            $success['name'] =  $user->name;
            $success['mobile'] =  $user->mobile;
            $success['userMenu'] = $userMenuService->getUserMenus();

            return $this->sendResponse($success, 'You have login successfully.');
            
        } 
        else{ 
            return $this->sendError('Invalid Credentials');
        } 
    }

    /**
     * Logout All Device
     */
    public function logout(){
        /** @var User $user */
        $user = Auth::user();
        $tokens =  $user->tokens->pluck('id');
        Token::whereIn('id', $tokens)
            ->update(['revoked'=> true]);

        RefreshToken::whereIn('access_token_id', $tokens)->update(['revoked' => true]);

        return $this->sendResponse(null, 'You have logout successfully.');
    }

    public function profile(){
        /** @var User $user */
        $user = Auth::user();
        return $this->sendResponse($user, null);
    }

}
