<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Input;

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
       // dd($user);
        if ($user->role != 'conductor') {
            return response()->json(['status' => 'Unauthorized', 'message' => 'You do not have the permission to view this page'])->setCallback(Input::get('callback'));
        }

        return $next($request);
    }

}
