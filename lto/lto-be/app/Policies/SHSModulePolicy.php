<?php

namespace App\Policies;

use App\Model\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Traits\Obfuscate\OptimusPolicy;
class SHSModulePolicy
{
    use HandlesAuthorization, OptimusPolicy;

    public function index(User $user)
    {
        return $this->accessable('SHS Modules');

    }
}
