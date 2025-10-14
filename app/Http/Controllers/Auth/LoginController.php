<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,staff',
        ]);

        $guard = $request->role === 'admin' ? 'web' : 'staff';

        $credentials = $request->only('email', 'password');

        if (Auth::guard($guard)->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(
                $request->role === 'admin' ? '/dashboard' : '/staff/dashboard'
            );
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        $guard = Auth::guard('web')->check() ? 'web' : 'staff';

        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
