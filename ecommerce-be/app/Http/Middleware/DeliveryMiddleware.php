<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\RoleTrait;
use App\Models\RoleUser;
use App\Models\MenuRole;
use App\Exceptions\AccessDeniedException;

class DeliveryMiddleware{
    use RoleTrait;
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {
        $userId = Auth::id();

        $hasRoleUser = RoleUser::where('user_id', $userId)->exists();

        if (!$hasRoleUser) {
            throw new AccessDeniedException('Unable to access');
        }

        $hasDeliveriesMenu = MenuRole::whereIn('role_id', function ($query) use ($userId) {
                $query->select('role_id')
                    ->from('role_user')
                    ->where('user_id', $userId);
            })
            ->whereHas('menu', function ($query) {
                $query->where('name', 'Deliveries');
            })
            ->exists();

        if (!$hasDeliveriesMenu) {
            throw new AccessDeniedException('Unable to access');
        }

        return $next( $request );
    }
}