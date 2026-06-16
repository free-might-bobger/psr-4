<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\User;
use App\Traits\Obfuscate\OptimusId;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\IndexResource;
use App\Services\UserService;
class UserController extends ApiController
{
    use OptimusId;
    public function __construct(UserRepository $repository, UserService $userService) {
        $this->model =  User::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest = BaseIndexRequest::class;
        $this->updateRequest = BaseIndexRequest::class;
        $this->showRequest = BaseIndexRequest::class;
        $this->userService = $userService;
    }

    public function getResource(): IndexResource {
        return new IndexResource($this->result);
    }

    public function showResource(): IndexResource {
        return new IndexResource($this->result);
    }

    public function changePassword($id): string {
        $request = app($this->storeRequest)->all();
        $this->userService->changePassword(
            $id,
            $request['password']
        );
        return response()->json('You have successfully update your password.');
    }

    public function inviteByEmail(Request $request): string {
        $this->userService->inviteByEmail(
            $request->storeId, 
            $request->email
        );
        return response()->json('You have successfully invited the user');
    }

}
