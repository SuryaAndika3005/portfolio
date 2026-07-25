<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/project/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');

Route::post('/contact', [PortfolioController::class, 'contact'])
    ->middleware('throttle:contact')
    ->name('contact.send');
