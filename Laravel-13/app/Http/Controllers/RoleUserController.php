<?php

namespace App\Http\Controllers;

use App\Repositories\RoleUserRepository;
use App\Models\RoleUser;
use App\Http\Requests\BaseIndexRequest;
use App\Http\Requests\RoleUserRequest;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleUserController extends ApiController
{
    public function __construct(RoleUserRepository $repository) {
        $this->model =  RoleUser::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->storeRequest    = RoleUserRequest::class;
        $this->updateRequest    = RoleUserRequest::class;
    }

    /**
     * Remove a user–role assignment (pivot row) by user id and role id.
     */
    public function destroyByPair(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);
        RoleUser::where('user_id', $validated['user_id'])
            ->where('role_id', $validated['role_id'])
            ->delete();

        return response()->noContent();
    }
}
