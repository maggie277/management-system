<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        config(['session.cookie' => config('session.staff_cookie')]);
        return $next($request);
    }
}
