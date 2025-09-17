<?php

namespace App\Policies;

use App\Model\User;
use App\Traits\Obfuscate\OptimusPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurriculumPolicy
{
    use HandlesAuthorization, OptimusPolicy;

    public function index(User $user)
    {
        return $this->accessable('Teacher');

    }

}
