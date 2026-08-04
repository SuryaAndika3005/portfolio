<?php

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');

Route::post('/contact', [PortfolioController::class, 'contact'])
    ->middleware('throttle:10,1')
    ->name('contact.send');

// Admin authentication (built-in Laravel session auth, no extra package).
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('admin.login.store');
});
Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

// Admin CRUD for projects.
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('projects', AdminProjectController::class)->except(['show']);
});
