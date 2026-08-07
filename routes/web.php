<?php

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');

// Rute untuk menangani pengiriman form kontak
Route::post('/contact', [PortfolioController::class, 'contact'])
    ->middleware('throttle:contact')
    ->name('contact.send');

Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
    Route::resource('projects', AdminProjectController::class)->except(['show']);
});
