<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreMenuAccess;
use App\Traits\RoleTrait;
use App\Enum\HttpAccessRight;
use App\Traits\Obfuscate\OptimusId;
use App\Enum\StoreMenu;


class ItemMiddleware {
    use RoleTrait, OptimusId;
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    /**
     * required filters=store_id:15647464 //(optimus_id)
     * if store_id is not provided, return 403
     * if store_id is provided, check if the user has access to the store
     * if the user has access to the store, return the next request
     * if the user does not have access to the store, return 403
     */
    public function handle( Request $request, Closure $next ): Response {
        if ( $this->isSuperAdmin() ) {
            return $next( $request );
        }
        
        $filters = $request->filters;
        if ( empty( $filters ) ) {
            throw new \App\Exceptions\AccessDeniedException( 'Filters parameter is required' );
        }

        $parts = $this->pregSplit( '/:/', $filters );
        if ( count( $parts ) !== 2 ) {
            throw new \App\Exceptions\AccessDeniedException( 'Invalid filters format. Expected: key:value' );
        }

        [ $storeKey, $encodedStoreId ] = $parts;
        $storeId = $this->optimus()->decode( $encodedStoreId );

        $method = $request->method();
        $userId = Auth::id();

        $accessRightId = $this->getAccessRightId( $method );

        $hasAccess = StoreMenuAccess::where( 'user_id', $userId )
            ->where( 'store_id', $storeId )
            ->where( 'store_menu_id', StoreMenu::ITEMS )
            ->where( 'access_right_id', $accessRightId )
            ->exists();

        if ( !$hasAccess ) {
            throw new \App\Exceptions\AccessDeniedException( 'Access Denied!' );
        }

        return $next( $request );
    }

    /**
     * Get access right ID based on HTTP method
     */
    private function getAccessRightId( string $method ): int {
        return match ( strtoupper( $method ) ) {
            'GET' => HttpAccessRight::GET,
            'POST' => HttpAccessRight::POST,
            'PUT' => HttpAccessRight::PUT,
            'PATCH' => HttpAccessRight::PATCH,
            'DELETE' => HttpAccessRight::DELETE,
            default => throw new \App\Exceptions\AccessDeniedException( 'Unsupported HTTP method' ),
        };
    }
}
