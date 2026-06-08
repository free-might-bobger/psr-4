<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use App\Models\User;
use App\Enum\Role;
use App\Services\UserMenuService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\Models\RoleUser;
use App\Services\RegisterService;
use Throwable;
use App\Traits\Frontend\FrontendUrlTrait;
class RegisterController extends BaseController
{
    use FrontendUrlTrait;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required_without_all:firstname,lastname|string|max:255',
            'firstname' => 'required_without:name|string|max:255',
            'lastname' => 'required_without:name|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = [];
        $input['email'] = $request->email;
        $input['password'] = bcrypt($request->password);
        $input['status'] = 0;
        $input['activation_code'] = Str::random(64);

        if ($request->filled('name')) {
            $trimmed = trim((string) $request->name);
            $parts = preg_split('/\s+/u', $trimmed, 2, PREG_SPLIT_NO_EMPTY);
            $input['firstname'] = $parts[0] ?? $trimmed;
            $input['lastname'] = $parts[1] ?? '-';
            $input['name'] = isset($parts[1]) ? $trimmed : $input['firstname'];
        } else {
            $input['firstname'] = $request->firstname;
            $input['lastname'] = $request->lastname;
            $input['name'] = trim($input['firstname'].' '.$input['lastname']);
        }

        /** @var User $user */
        $user = User::create($input);
        $success['name'] = $user->name;
        $success['success'] = true;
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(UserRequest $request)
    {
        $user = null;
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->filled('mobile')) {
            $user = User::where('mobile', $request->mobile)->first();
        }

        if (!$user) {
            return $this->sendError('Invalid Credentials', [], 401);
        }

        if (array_key_exists('status', $user->getAttributes()) && ! $user->status) {
            return $this->sendError('Please verify your number.');
        }

        $credentials = ['password' => $request->password, 'status' => 1];
        if ($request->filled('email')) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['mobile'] = $request->mobile;
        }

        if (Auth::attempt($credentials)) {
            $userMenuService = app(UserMenuService::class);
            /** @var User $user */
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['optimus_id'] = $user->optimus_id;
            $success['name'] = $user->name;
            $success['mobile'] = $user->mobile ?? '';
            $success['userMenu'] = json_encode($userMenuService->getUserMenus());

            return $this->sendResponse($success, 'You have login successfully.');
        }

        return $this->sendError('Invalid Credentials', [], 401);
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

    public function facebook()
    {
        $callbackUrl = url('/auth/facebook/callback');

        return Socialite::driver('facebook')
            ->redirectUrl($callbackUrl)
            ->scopes(['email'])
            ->stateless()
            ->redirect();
    }

    public function facebookCallback()
    {
        try {
            $callbackUrl = url('/auth/facebook/callback');
            $fbUser = Socialite::driver('facebook')
                ->redirectUrl($callbackUrl)
                ->stateless()
                ->user();
            $email = $fbUser->getEmail();

            if (!$email) {
                return redirect($this->getFrontendLoginRedirect([
                    'error' => 'facebook_email_required',
                ]));
            }

            /** @var User $user */
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $fbUser->getName() ?: $fbUser->getNickname() ?: 'Facebook User';
            $user->email = $email;
            $user->status = 1;


            if (!$user->exists) {
                $user->password = bcrypt(Str::random(40));
            }

            if (isset($user->email_verified_at) && !$user->email_verified_at) {
                $user->email_verified_at = now();
            }

            $user->save();

            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => Role::CUSTOMER
            ]);
            Auth::login($user);

            $userMenuService = app(UserMenuService::class);
            $token = $user->createToken('facebook-login')->accessToken;

            Log::info('Facebook login successful', [
                'user' => $user,
                'token' => $token,
                'optimus_id' => $user->optimus_id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'userMenu' => json_encode($userMenuService->getUserMenus()),
            ]);
            return redirect($this->getFrontendLoginRedirect([
                'token' => $token,
                'optimus_id' => $user->optimus_id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'userMenu' => json_encode($userMenuService->getUserMenus()),
                'provider' => 'facebook',
            ]));

        } catch (Throwable $e) {
            Log::error('Facebook login failed', [
                'message' => $e->getMessage(),
                'callback_url' => url('/auth/facebook/callback'),
                'request_url' => request()->fullUrl(),
            ]);

            return redirect($this->getFrontendLoginRedirect([
                'error' => 'facebook_failed',
            ]));
        }
    }

    public function google()
    {
        $callbackUrl = url('/auth/google/callback');

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->scopes(['email'])
            ->stateless()
            ->redirect();
    }

    public function googleCallback()
    {
        try {
            $callbackUrl = url('/auth/google/callback');
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->stateless()
                ->user();
            $email = $googleUser->getEmail();

            if (!$email) {
                return redirect($this->getFrontendLoginRedirect([
                    'error' => 'google_email_required',
                ]));
            }

            /** @var User $user */
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User';
            $user->email = $email;
            $user->status = 1;

            if (!$user->exists) {
                $user->password = bcrypt(Str::random(40));
            }

            if (isset($user->email_verified_at) && !$user->email_verified_at) {
                $user->email_verified_at = now();
            }

            $user->save();

            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => Role::CUSTOMER
            ]);
            Auth::login($user);

            $userMenuService = app(UserMenuService::class);
            $token = $user->createToken('google-login')->accessToken;

            Log::info('Google login successful', [
                'user' => $user,
                'token' => $token,
                'optimus_id' => $user->optimus_id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'userMenu' => json_encode($userMenuService->getUserMenus()),
            ]);
            return redirect($this->getFrontendLoginRedirect([
                'token' => $token,
                'optimus_id' => $user->optimus_id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'userMenu' => json_encode($userMenuService->getUserMenus()),
                'provider' => 'google',
            ]));

        } catch (Throwable $e) {
            Log::error('Google login failed', [
                'message' => $e->getMessage(),
                'callback_url' => url('/auth/google/callback'),
                'request_url' => request()->fullUrl(),
            ]);

            return redirect($this->getFrontendLoginRedirect([
                'error' => 'google_failed',
            ]));
        }
    }

    public function loginPageRedirect()
    {
        return redirect($this->getFrontendLoginRedirect());
    }

    private function getFrontendLoginRedirect(array $query = []): string
    {
        $frontEndUrl = FrontendUrlTrait::getUrl();

        return $frontEndUrl . '/login?' . http_build_query($query);
    }

     public function verifyActivationCode(string $activationCode, RegisterService $service){

        $verified = $service->verifyActivationCode($activationCode);
        if($verified){
            return redirect(config('app.frontend_url') . '?success=1');
        }
        return redirect(config('app.frontend_url') . '?success=0');
    }

}
