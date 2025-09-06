<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\User;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    use OptimusRequiredToModel;
    public function __construct(UserRepository $repository) {
        $this->model =  User::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = BaseIndexRequest::class;
        $this->updateRequest    = BaseIndexRequest::class;
    }

    public function isPublicRoute(string $routeName): Bool {
        return true;
    }

    public function changePassword($id){
        $this->params = app($this->storeRequest)->all();
        $user = User::where('id', $this->optimus()->decode($id))->first();
        if($user){
            $user->password = \Hash::make(Arr::get('password', $this->params));
            $user->update();
        }
        return response()->json('You have successfully update your password.');
    }

    public function inviteByEmail(Request $request){
        $this->repository->inviteByEmail($request->storeId, $request->email);
        return response()->json('You have successfully invited the user');
    }

    protected function getResource() : BaseResource {
        return new BaseResource( $this->result );
    }
}
