<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user->role !='admin') {
           '{"status":"Unauthorized","message":"You do not havethe permission to view this page"}' ;
        }

        return $next($request);
    }
}