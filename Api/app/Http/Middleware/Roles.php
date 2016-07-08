<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Roles {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $user = Auth::user();
        dd($user);
        if ($user->role != 'conductor') {
            return response()
                            ->json(['status' => 'Unauthorized', 'message' => 'You do not have the permission to view this page']);
        }

        return $next($request);
    }

}
