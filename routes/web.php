<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Staff\StaffLoginController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\TaskController;
use App\Http\Controllers\AdminUserController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RedirectIfStaffAuthenticated;

// ---------------- ADMIN ROUTES ----------------

// Root redirects to login
Route::get('/', fn() => redirect()->route('login'));

// Admin login (guest only)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Admin logout (authenticated)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:web')
    ->name('logout');

// Admin dashboard (authenticated)
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth:web', 'verified'])
    ->name('dashboard');

// Admin authenticated routes
Route::middleware(['auth:web'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('documents-list', [DocumentController::class, 'list'])->name('documents.list');

    // Assets
    Route::resource('assets', AssetController::class);
    Route::get('assets-list', [AssetController::class, 'list'])->name('assets.list');

    // Donors, budgets, expenses
    Route::resource('donors', DonorController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);

    // Admin Only: Manage Users
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/users/create', [AdminUserController::class, 'create'])
            ->name('admin.users.create');
        Route::post('/users/store', [AdminUserController::class, 'store'])
            ->name('admin.users.store');
    });
});

// ---------------- STAFF ROUTES ----------------
Route::prefix('staff')->group(function () {
    // Staff login (guest only) with custom middleware
    Route::middleware([RedirectIfStaffAuthenticated::class])->group(function () {
        Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
        Route::post('/login', [StaffLoginController::class, 'login'])->name('staff.login.submit');
    });

    // Staff authenticated routes
    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');

        // Tasks
        Route::get('/tasks', [TaskController::class, 'index'])->name('staff.tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('staff.tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('staff.tasks.store');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('staff.tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('staff.tasks.update');
        Route::get('/tasks/deadlines', [TaskController::class, 'deadlines'])->name('staff.tasks.deadlines');
        Route::get('/tasks/pending', [TaskController::class, 'pending'])->name('staff.tasks.pending');
    });
});

require __DIR__.'/auth.php';

// ---------------- DEV ONLY ROUTES ----------------
// Use these only on local/dev to bypass login

// Auto-login as admin by ID
Route::get('/dev/login-as/{id}', function ($id) {
    if (! App::environment(['local', 'development'])) {
        abort(403, 'Forbidden in production');
    }

    $user = User::find($id);
    if (! $user) {
        abort(404, 'User not found');
    }

    Auth::guard('web')->loginUsingId($id);
    request()->session()->regenerate();

    return redirect()->intended('/dashboard');
});

// Directly view dashboard without auth (UI only, no Auth::user())
Route::get('/dev/dashboard', function () {
    if (! App::environment(['local', 'development'])) {
        abort(403, 'Forbidden in production');
    }

    return view('dashboard');
});
