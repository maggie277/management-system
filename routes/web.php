<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\User;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Staff\StaffLoginController;
use App\Http\Controllers\Staff\TaskController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminTaskController;

// Middleware
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RedirectIfStaffAuthenticated;

// Dashboard Controllers (Aliased)
use App\Http\Controllers\DashboardController as AdminDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;

// -------------------------------
// 🔐 AUTH & LOGIN
// -------------------------------
Route::get('/', fn() => redirect()->route('login.form'));

// Unified login for guests
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});

// Logout (works for both guards)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// -------------------------------
// -------------------------------
// 🟢 ADMIN ROUTES (web guard)
// -------------------------------
Route::middleware(['auth:web'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('documents-list', [DocumentController::class, 'list'])->name('documents.list');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Assets
    Route::resource('assets', AssetController::class);
    Route::get('assets/list', [AssetController::class, 'list'])->name('assets.list');

    // Donors, Budgets, Expenses
    Route::resource('donors', DonorController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);

    // Admin User Management
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [AdminUserController::class, 'store'])->name('admin.users.store');

    // Staff List Route - CORRECT PLACEMENT
    Route::get('/admin/staff', [AdminUserController::class, 'staffList'])->name('admin.staff.list');

    // Admin Tasks
    Route::prefix('admin/tasks')->name('admin.tasks.')->group(function () {
        Route::get('/', [AdminTaskController::class, 'index'])->name('index');
        Route::get('/create/{staff?}', [AdminTaskController::class, 'create'])->name('create');
        Route::post('/store', [AdminTaskController::class, 'store'])->name('store');
        Route::get('/{task}/edit', [AdminTaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [AdminTaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [AdminTaskController::class, 'destroy'])->name('destroy');
    });


    // Admin User Management
     Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [AdminUserController::class, 'store'])->name('admin.users.store');
});

// -------------------------------
// 👷 STAFF ROUTES (staff guard)
// -------------------------------
Route::prefix('staff')->group(function () {

    // Staff login (guest only)
    Route::middleware([RedirectIfStaffAuthenticated::class])->group(function () {
        Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
        Route::post('/login', [StaffLoginController::class, 'login'])->name('staff.login.submit');
    });

    // Authenticated staff
    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

        // Staff Tasks
        Route::get('/tasks', [TaskController::class, 'index'])->name('staff.tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('staff.tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('staff.tasks.store');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('staff.tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('staff.tasks.update');
        Route::get('/tasks/deadlines', [TaskController::class, 'deadlines'])->name('staff.tasks.deadlines');
        Route::get('/tasks/pending', [TaskController::class, 'pending'])->name('staff.tasks.pending');
    });
});

// -------------------------------
// ⚙️ DEV ONLY (local testing shortcuts)
// -------------------------------
require __DIR__ . '/auth.php';

// Dev shortcut login (local)
Route::get('/dev/login-as/{id}', function ($id) {
    if (!App::environment(['local', 'development'])) {
        abort(403, 'Forbidden in production');
    }

    $user = User::find($id);
    if (!$user) abort(404, 'User not found');

    Auth::guard('web')->loginUsingId($id);
    request()->session()->regenerate();

    return redirect()->intended('/dashboard');
});

// Dev dashboard view
Route::get('/dev/dashboard', function () {
    if (!App::environment(['local', 'development'])) {
        abort(403, 'Forbidden in production');
    }

    return view('dashboard');
});
