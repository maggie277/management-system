<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AssetController; // Add AssetController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Documents section
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index'); // Documents dashboard
    Route::get('/documents/upload', [DocumentController::class, 'create'])->name('documents.create'); // Upload form
    Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.store'); // Store uploaded document
    Route::get('/documents/list', [DocumentController::class, 'list'])->name('documents.list'); // List all documents
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit'); // Edit form
    Route::patch('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update'); // Update document
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy'); // Delete document

    // Assets section
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); // Assets dashboard
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create'); // Add asset
    Route::post('/assets/create', [AssetController::class, 'store'])->name('assets.store'); // Store asset
    Route::get('/assets/list', [AssetController::class, 'list'])->name('assets.list'); // List all assets
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit'); // Edit asset
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update'); // Update asset
    Route::match(['put','patch'], '/assets/{id}', [AssetController::class, 'update'])->name('assets.update');

    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy'); // Delete asset
    // Donors section
    Route::resource('donors', DonorController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);
});

require __DIR__.'/auth.php';
