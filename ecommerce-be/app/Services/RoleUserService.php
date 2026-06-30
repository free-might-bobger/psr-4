<?php

namespace App\Services;

use App\Repositories\RoleUserRepository;

class RoleUserService
{
    private RoleUserRepository $repository;

    public function __construct(RoleUserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function destroyByPair(int $userId, int $roleId): void
    {
        $this->repository->deleteWhere([
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }
}