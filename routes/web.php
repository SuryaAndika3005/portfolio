<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{id}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');

// Rute untuk menangani pengiriman form kontak
Route::post('/contact', [PortfolioController::class, 'contact'])->name('contact.send');