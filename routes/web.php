<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\WelcomeController::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
