<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (Auth::user()->role != $role) {
            return response('Unauthorized action.', 403);
        }

        return $next($request);
    }
}