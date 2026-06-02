<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\Role\AccessChecker;
class CustomerMiddleware {
    use AccessChecker;
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( Request $request, Closure $next ): Response {
        if ( !$this->isCustomer() ) {
            throw new \App\Exceptions\AccessDeniedException( 'Access Denied!' );
        }
        return $next( $request );
    }
}
