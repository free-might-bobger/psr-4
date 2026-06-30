<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\StoreUser;
use App\Exceptions\AccessDeniedException;

class StoreDeliveryMiddleware {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {
        $userId = Auth::id();

        $hasStoreUser = StoreUser::where('user_id', $userId)->exists();

        if (!$hasStoreUser) {
            throw new AccessDeniedException('Unable to access');
        }

        $hasDeliveriesMenu = StoreUser::where('user_id', $userId)
            ->whereHas('storeUserMenu.storeMenu', function ($query) {
                $query->where('name', 'Deliveries');
            })
            ->exists();

        if (!$hasDeliveriesMenu) {
            throw new AccessDeniedException('Unable to access');
        }

        return $next( $request );
    }
}
