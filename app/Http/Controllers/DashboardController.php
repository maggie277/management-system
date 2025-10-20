<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Asset;
use App\Models\Document;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totals = [
                'donors' => Donor::count(),
                'assets' => Asset::count(),
                'documents' => Document::count(),
                'staff' => Staff::count() + User::where('role', 'admin')->count(),
            ];

            return view('dashboard', compact('totals'));
        } catch (\Exception $e) {
            // Temporary fallback for debugging
            $totals = [
                'donors' => Donor::count(),
                'assets' => Asset::count(),
                'documents' => 0, // Fallback value
                'staff' => Staff::count() + User::where('role', 'admin')->count(),
            ];

            return view('dashboard', compact('totals'));
        }
    }

    public function stats()
    {
        return response()->json([
            'donors' => Donor::count(),
            'assets' => Asset::count(),
            'documents' => Document::count(),
            'staff' => Staff::count() + User::where('role', 'admin')->count(),
        ]);
    }
}
