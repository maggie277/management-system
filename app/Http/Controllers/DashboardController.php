<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // For now, everyone uses the same dashboard
        // We'll add department-specific content later
        return view('dashboard', [
            'user' => $user
        ]);
    }
}
