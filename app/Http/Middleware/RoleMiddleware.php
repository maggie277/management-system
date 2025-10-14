<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Comma-separated roles
     * @param  string|null $guard Optional guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles, $guard = null)
    {
        $guard = $guard ?: 'web';
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return redirect()->route($guard === 'staff' ? 'staff.login' : 'login.form');
        }

        $allowed = array_map('trim', explode(',', $roles));

        if (!in_array($user->role, $allowed)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
