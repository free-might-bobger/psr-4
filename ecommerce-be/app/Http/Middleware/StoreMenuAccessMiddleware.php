<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\Obfuscate\OptimusRequiredToModel;

class StoreMenuAccessMiddleware
{
    use OptimusRequiredToModel;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
        * change store_id and user_id from optimus_id to real id. 
        */
        $request->merge([
            'store_id' => $this->optimus()->decode($request->store_id),
            'user_id'  => $this->optimus()->decode($request->user_id)
        ]);
        
        return $next($request);
    }
}
