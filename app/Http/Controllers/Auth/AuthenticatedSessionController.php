<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the admin login form
     */
    public function create()
    {
        return view('auth.login'); // admin login blade
    }

    /**
     * Handle admin login
     */
    public function store(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Try to log in with web guard
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::guard('web')->user();

            // Check if role column exists and matches "admin"
            if (property_exists($user, 'role')) {
                if (strtolower($user->role) !== 'admin') {
                    Auth::guard('web')->logout();
                    return back()->withErrors([
                        'email' => "This account is not an admin. Role found: {$user->role}",
                    ]);
                }
            } else {
                // If no role column, block login for safety
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'email' => 'This account does not have a role defined.',
                ]);
            }

            // Passed all checks → regenerate session
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Invalid login
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    /**
     * Logout admin
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
