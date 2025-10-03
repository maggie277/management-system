<?php

use Illuminate\Support\Facades\Route;
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

// ---------------- ADMIN ROUTES ----------------

// Root redirects to login
Route::get('/', fn() => redirect()->route('login'));

// Admin login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Admin dashboard
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin authenticated routes
Route::middleware('auth')->group(function () {
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
});

// ---------------- STAFF ROUTES ----------------
Route::prefix('staff')->group(function () {
    // Staff login (guest only)
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [StaffLoginController::class, 'showLoginForm'])->name('staff.login');
        Route::post('/login', [StaffLoginController::class, 'login'])->name('staff.login.submit');
    });

    // Staff logout & dashboard (authenticated)
    Route::middleware('auth:staff')->group(function () {
Route::post('/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
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
