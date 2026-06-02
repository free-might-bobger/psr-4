<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\RoleTrait;
class AllTransactionsMiddleware {
    use RoleTrait;
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {

        if ( !$this->isSuperAdmin() ) {
            throw new \App\Exceptions\AccessDeniedException( 'Access Denied!' );
        }
        return $next( $request );
    }
}
