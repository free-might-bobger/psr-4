<?php

namespace App\Http\Controllers;

use App\Repositories\StoreUserRepository;
use App\Http\Requests\StoreUser\IndexRequest;
use App\Http\Requests\StoreUser\StoreRequest;
use App\Http\Requests\StoreUser\UpdateRequest;
use App\Traits\Frontend\FrontendUrlTrait;
use App\Services\StoreUserService;

class StoreUserController extends ApiController
{
    public function __construct(StoreUserRepository $repository){
        $this->repository = $repository;
        $this->indexRequest = IndexRequest::class;
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
    }

    public function inviteUser(){

        $request = app($this->storeRequest);
        $this->repository->inviteUser([
            'store_id' => $request->store_id,
            'email' => $request->email
        ]);
        
        return response()->json([
            'message' => 'User invited successfully'
        ]);
    }

     public function acceptStoreInvitation(string $activationCode, StoreUserService $service){

        $storeUser = $service->verifyActivationCode($activationCode);
        if($storeUser){
            return redirect(config('app.frontend_url') . '/dashboard/my-stores/' . $storeUser->store_optimus_id . '/users?success=1');
        }
        return redirect(config('app.frontend_url') . 'error-page?success=0');
    }

}
