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
use App\Http\Controllers\DonorController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\InstitutionalDocumentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ConsultantController;



// Redirect root to login
Route::redirect('/', '/login');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
// ========= ADD THESE PASSWORD RESET ROUTES =========
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // ... your existing protected routes ...

});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('dashboard');

    // ========= DOCUMENT ROUTES =========
    Route::get('/documents-list', [DocumentController::class, 'list'])->name('documents.list');
    Route::get('/documents/category/{category}', [DocumentController::class, 'byCategory'])->name('documents.category');
    Route::get('/documents/folder/{categoryId}/{folderId}', [DocumentController::class, 'openFolder'])->name('documents.folder');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // Folder Routes
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/categories/{category}/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');

    // ========= ASSET ROUTES =========
    Route::resource('assets', AssetController::class);
    Route::get('/asset-register', function(Request $request) {
        $assets = Asset::orderBy('name')->get();
        $years = DepreciationYear::getActiveYears();
        $currentYear = date('Y');
        return view('assets.list', compact('assets', 'years', 'currentYear'));
    })->name('asset-register');

    // Depreciation Year Management Routes
    Route::prefix('depreciation-years')->name('depreciation-years.')->group(function () {
        Route::get('/', function() {
            $years = DepreciationYear::orderBy('position')->get();
            return view('assets.years-management', compact('years'));
        })->name('index');

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

        Route::post('/reorder', function(Request $request) {
            $request->validate(['years' => 'required|array']);
            DepreciationYear::updatePositions($request->years);
            return response()->json(['success' => true]);
        })->name('reorder');

        Route::post('/{id}/update-style', function(Request $request, $id) {
            $year = DepreciationYear::findOrFail($id);
            $year->update([
                'background_color' => $request->background_color,
                'text_color' => $request->text_color
            ]);
            return response()->json(['success' => true]);
        })->name('update-style');

        Route::post('/{id}/toggle', function($id) {
            $year = DepreciationYear::findOrFail($id);
            $year->update(['is_active' => !$year->is_active]);
            $action = $year->is_active ? 'enabled' : 'disabled';
            return redirect()->route('depreciation-years.index')
                ->with('success', "Year {$year->year} column {$action}");
        })->name('toggle');

        Route::delete('/{id}', function($id) {
            $year = DepreciationYear::findOrFail($id);
            $deletedYear = $year->year;
            $year->delete();
            $years = DepreciationYear::orderBy('position')->get();
            foreach ($years as $index => $year) {
                $year->update(['position' => $index + 1]);
            }
            return redirect()->route('depreciation-years.index')
                ->with('success', "Year {$deletedYear} column deleted");
        })->name('delete');
    });

    Route::resource('non-depreciable-assets', NonDepreciableAssetController::class);
    Route::get('/assets/category/{category}', [AssetController::class, 'byCategory'])->name('assets.category');
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/maintenance', [AssetController::class, 'maintenance'])->name('assets.maintenance');

    // ========= CALENDAR ROUTES =========
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
    Route::get('/calendar/events/{event}/edit', [CalendarController::class, 'edit'])->name('calendar.events.edit');
    Route::put('/calendar/events/{event}', [CalendarController::class, 'update'])->name('calendar.events.update');
    Route::delete('/calendar/events/{event}', [CalendarController::class, 'destroy'])->name('calendar.events.destroy');
    Route::get('/calendar/events/{event}/delete', [CalendarController::class, 'destroy'])->name('calendar.events.delete');

    // ========= INSTITUTIONAL DOCUMENTS =========
    Route::prefix('institutional-documents')->name('documents.institutional.')->group(function () {
        Route::get('/', [InstitutionalDocumentController::class, 'index'])->name('index');
        Route::get('/create', [InstitutionalDocumentController::class, 'create'])->name('create');
        Route::post('/', [InstitutionalDocumentController::class, 'store'])->name('store');
        Route::get('/{id}', [InstitutionalDocumentController::class, 'show'])->name('show');
        Route::delete('/{id}', [InstitutionalDocumentController::class, 'destroy'])->name('destroy');
    });

// Weekly Task Planning Routes
Route::prefix('weekly-plans')->name('weekly-plans.')->group(function () {
    Route::post('/', [TaskController::class, 'storeWeeklyPlan'])->name('store');
    Route::post('/{weeklyPlan}', [TaskController::class, 'updateWeeklyPlan'])->name('update');
    Route::get('/user/{userId?}', [TaskController::class, 'getWeeklyPlan'])->name('get');
    Route::get('/all', [TaskController::class, 'getAllWeeklyPlans'])->name('all');
});

    // ========= BUDGET ROUTES =========
    Route::resource('budgets', BudgetController::class);
    Route::post('/budgets/{budget}/duplicate', [BudgetController::class, 'duplicate'])->name('budgets.duplicate');
    Route::post('/budgets/{budget}/change-status', [BudgetController::class, 'changeStatus'])->name('budgets.change-status');
    Route::get('/budgets/{budget}/export-pdf', [BudgetController::class, 'exportPdf'])->name('budgets.export-pdf');
    Route::get('/budgets/api/statistics', [BudgetController::class, 'getStatistics'])->name('budgets.api.statistics');
    Route::get('/budgets/api/status-data', [BudgetController::class, 'getStatusData'])->name('budgets.api.status-data');
    Route::post('/budgets/search', [BudgetController::class, 'search'])->name('budgets.search');

    // ========= EXPENSE ROUTES =========
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('expenses/{expense}/mark-paid', [ExpenseController::class, 'markAsPaid'])->name('expenses.mark-paid');

    // Expense API Routes
    Route::get('/api/budget-items/{budget}', [ExpenseController::class, 'getBudgetItems'])->name('api.budget-items');
    Route::get('/api/budget-remaining/{budget}', [ExpenseController::class, 'getBudgetRemaining'])->name('api.budget-remaining');

    // ========= DONOR ROUTES =========
    Route::get('/donors-list', [DonorController::class, 'list'])->name('donors.list');
    Route::resource('donors', DonorController::class);
    Route::get('/donors/{donor}/download-document', [DonorController::class, 'downloadDocument'])->name('donors.download-document');
    Route::post('/donors/{donor}/toggle-status', [DonorController::class, 'toggleStatus'])->name('donors.toggle-status');
    Route::resource('consultants', ConsultantController::class);
    Route::get('consultants/{consultant}/toggle-status', [ConsultantController::class, 'toggleStatus'])->name('consultants.toggle-status');
    // ========= TASK ROUTES =========
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/status-update', [TaskController::class, 'addStatusUpdate'])->name('tasks.status-update');
    Route::post('/tasks/{task}/quick-complete', [TaskController::class, 'quickComplete'])->name('tasks.quick-complete');
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::put('/tasks/{task}/review', [TaskController::class, 'submitReview'])->name('tasks.submitReview');

    // ========= DEBUG ROUTES =========
    Route::get('/debug-init-history', function() {
        $tasks = \App\Models\Task::whereNull('status_history')->get();
        foreach ($tasks as $task) {
            $task->update([
                'status_history' => [
                    [
                        'user_type' => 'assigner',
                        'user_id' => $task->assigned_by,
                        'message' => 'Task created and assigned',
                        'timestamp' => $task->created_at->toDateTimeString(),
                        'new_status' => 'pending'
                    ]
                ]
            ]);
        }
        return "Initialized history for " . $tasks->count() . " tasks";
    });

    // ========= API ROUTES =========
    Route::prefix('api')->group(function () {
        Route::get('/assets/{asset}/depreciation/{year}', function($assetId, $year) {
            $asset = Asset::findOrFail($assetId);
            return response()->json([
                'annual_depreciation' => $asset->getDepreciationForYear($year),
                'accumulated_depreciation' => $asset->calculateAccumulatedDepreciation($year),
                'net_book_value' => $asset->calculateNetBookValue($year)
            ]);
        });
        Route::get('/depreciation-years', function() {
            $years = DepreciationYear::getActiveYears();
            return response()->json($years);
        });
    });

});
