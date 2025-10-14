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
        $totals = [
            'donors' => Donor::count(),
            'assets' => Asset::count(),
            'documents' => Document::count(),
            'staff' => Staff::count() + User::where('role', 'admin')->count(), // Count both staff and admins
        ];

        return view('dashboard', compact('totals'));
    }

    public function stats()
    {
        return response()->json([
            'donors' => Donor::count(),
            'assets' => Asset::count(),
            'documents' => Document::count(),
            'staff' => Staff::count() + User::where('role', 'admin')->count(), // Count both staff and admins
        ]);
    }
}
