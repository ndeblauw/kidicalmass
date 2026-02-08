<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Route::resource('articles', \App\Http\Controllers\ArticleController::class)->only(['index', 'show']);
Route::resource('activities', \App\Http\Controllers\ActivityController::class)->only(['index', 'show']);
Route::resource('groups', \App\Http\Controllers\GroupController::class)->only(['index', 'show']);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Impersonation routes (authenticated users with Filament admin access)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('impersonate/{user}', [\App\Http\Controllers\ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [\App\Http\Controllers\ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';
