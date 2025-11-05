<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\AssetController;
use App\Models\Asset;
use App\Models\DepreciationYear;
 use App\Http\Controllers\NonDepreciableAssetController;

// Redirect root to login
Route::redirect('/', '/login');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ====================
    // DOCUMENT ROUTES
    // ====================
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents-list', [DocumentController::class, 'list'])->name('documents.list');
    Route::get('/documents/category/{category}', [DocumentController::class, 'byCategory'])->name('documents.category');

    // ====================
    // FOLDER ROUTES
    // ====================
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');

    // ====================
    // ASSET ROUTES
    // ====================
    Route::resource('assets', AssetController::class);

    // ====================
    // ASSET REGISTER ROUTES
    // ====================

    // Main Asset Register Route
    Route::get('/asset-register', function(Request $request) {
        $assets = Asset::orderBy('name')->get();
        $years = DepreciationYear::getActiveYears();
        $currentYear = date('Y');

        return view('assets.list', compact('assets', 'years', 'currentYear'));
    })->name('asset-register');

    // ====================
    // DEPRECIATION YEAR MANAGEMENT ROUTES
    // ====================

    Route::prefix('depreciation-years')->name('depreciation-years.')->group(function () {
        // Year management page
        Route::get('/', function() {
            $years = DepreciationYear::orderBy('position')->get();
            return view('assets.years-management', compact('years'));
        })->name('index');

        // Add new year
        Route::post('/add', function(Request $request) {
            $request->validate([
                'year' => 'required|integer|min:2011|max:2100',
                'position' => 'nullable|integer|min:1'
            ]);

            try {
                DepreciationYear::addYear($request->year, $request->position);
                return redirect()->route('depreciation-years.index')
                    ->with('success', 'Year column added successfully');
            } catch (\Exception $e) {
                return redirect()->route('depreciation-years.index')
                    ->with('error', 'Year already exists in the table');
            }
        })->name('add');

        // Reorder years
        Route::post('/reorder', function(Request $request) {
            $request->validate([
                'years' => 'required|array'
            ]);

            DepreciationYear::updatePositions($request->years);

            return response()->json(['success' => true]);
        })->name('reorder');

        // Update column style
        Route::post('/{id}/update-style', function(Request $request, $id) {
            $year = DepreciationYear::findOrFail($id);

            $year->update([
                'background_color' => $request->background_color,
                'text_color' => $request->text_color
            ]);

            return response()->json(['success' => true]);
        })->name('update-style');

        // Toggle year visibility
        Route::post('/{id}/toggle', function($id) {
            $year = DepreciationYear::findOrFail($id);
            $year->update(['is_active' => !$year->is_active]);

            $action = $year->is_active ? 'enabled' : 'disabled';
            return redirect()->route('depreciation-years.index')
                ->with('success', "Year {$year->year} column {$action}");
        })->name('toggle');

        // Delete year
        Route::delete('/{id}', function($id) {
            $year = DepreciationYear::findOrFail($id);
            $deletedYear = $year->year;
            $year->delete();

            // Reorder remaining years
            $years = DepreciationYear::orderBy('position')->get();
            foreach ($years as $index => $year) {
                $year->update(['position' => $index + 1]);
            }

            return redirect()->route('depreciation-years.index')
                ->with('success', "Year {$deletedYear} column deleted");
        })->name('delete');
    });

    // ====================
    // ADDITIONAL ASSET ROUTES
    // ====================




// New non-depreciable asset routes
Route::resource('non-depreciable-assets', NonDepreciableAssetController::class);

// Update dashboard route to show both types
Route::get('/dashboard', function () {
    $depreciableAssets = \App\Models\Asset::count();
    $nonDepreciableAssets = \App\Models\NonDepreciableAsset::count();
    $totalAssets = $depreciableAssets + $nonDepreciableAssets;

    return view('dashboard', compact('totalAssets', 'depreciableAssets', 'nonDepreciableAssets'));
})->name('dashboard');

    // Quick asset actions
    Route::get('/assets/category/{category}', [AssetController::class, 'byCategory'])->name('assets.category');
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/maintenance', [AssetController::class, 'maintenance'])->name('assets.maintenance');

    // ====================
    // API ROUTES FOR AJAX
    // ====================

    Route::prefix('api')->group(function () {
        // Get asset depreciation data for specific year
        Route::get('/assets/{asset}/depreciation/{year}', function($assetId, $year) {
            $asset = Asset::findOrFail($assetId);

            return response()->json([
                'annual_depreciation' => $asset->getDepreciationForYear($year),
                'accumulated_depreciation' => $asset->calculateAccumulatedDepreciation($year),
                'net_book_value' => $asset->calculateNetBookValue($year)
            ]);
        });

        // Get all years data
        Route::get('/depreciation-years', function() {
            $years = DepreciationYear::getActiveYears();
            return response()->json($years);
        });
    });

    // ====================
    // TEST ROUTES (Remove in production)
    // ====================

    Route::get('/test-assets', function() {
        return response()->json([
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('status', 'active')->count(),
            'years_configured' => DepreciationYear::count()
        ]);
    });

    Route::get('/test-depreciation/{assetId}', function($assetId) {
        $asset = Asset::findOrFail($assetId);
        $currentYear = date('Y');

        return response()->json([
            'asset' => $asset->name,
            'cost' => $asset->cost,
            'depreciation_rate' => $asset->depreciation_rate,
            'annual_depreciation' => $asset->calculateAnnualDepreciation(),
            'accumulated_depreciation' => $asset->calculateAccumulatedDepreciation($currentYear),
            'net_book_value' => $asset->calculateNetBookValue($currentYear)
        ]);
    });
});
