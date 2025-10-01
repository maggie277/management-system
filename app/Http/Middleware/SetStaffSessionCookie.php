<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetStaffSessionCookie
{
    public function handle(Request $request, Closure $next)
    {
        // Only for staff routes
        if ($request->is('staff/*')) {
            config(['session.cookie' => config('session.staff_cookie')]);
        }

        return $next($request);
    }
}
