<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;

// Rute halaman utama (harus ada ->name('home') di ujungnya)
Route::get('/', [PortfolioController::class, 'index'])->name('home');

// Rute halaman detail
Route::get('/project/{id}', [PortfolioController::class, 'show'])->name('project.show');