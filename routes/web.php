<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');

Route::resource('articles', \App\Http\Controllers\ArticleController::class)->only(['index', 'show']);
Route::resource('activities', \App\Http\Controllers\ActivityController::class)->only(['index', 'show']);
Route::resource('groups', \App\Http\Controllers\GroupController::class)->only(['index', 'show']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');

    Route::resource('activities', \App\Http\Controllers\ActivityController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('articles', \App\Http\Controllers\ArticleController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('home/groups', \App\Http\Controllers\Home\GroupController::class)
        ->only(['show', 'edit', 'update'])
        ->names('home.groups');
});

// Impersonation routes (authenticated users with Filament admin access)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('impersonate/{user}', [\App\Http\Controllers\ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [\App\Http\Controllers\ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';
