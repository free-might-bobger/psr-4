<?php

namespace App\Observers;

use App\Models\RoleUser;
use Illuminate\Validation\ValidationException;

class RoleUserObserver
{
    public function creating(RoleUser $roleUser): void
    {
        $exists = RoleUser::where('user_id', $roleUser->user_id)
            ->where('role_id', $roleUser->role_id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'role_id' => 'This user already has this role assigned.'
            ]);
        }
    }
}
