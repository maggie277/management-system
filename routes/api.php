<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Folder;

// Add this route for fetching folders
Route::get('/categories/{category}/folders', function ($categoryId) {
    try {
        $folders = Folder::where('category_id', $categoryId)
            ->whereNull('parent_id') // Only top-level folders
            ->get(['id', 'name']);

        return response()->json($folders);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to load folders'], 500);
    }
});
