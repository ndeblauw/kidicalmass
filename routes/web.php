<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use Illuminate\Support\Facades\Route;

/*
 * PUBLIC SECTION
 */

Route::get('/', HomeController::class)->name('home');

Route::resource('articles', ArticleController::class)->only(['index', 'show']);
Route::resource('activities', ActivityController::class)->only(['index', 'show']);
Route::resource('groups', GroupController::class)->only(['index', 'show']);

/*
 * LOGGED IN SECTION
 */

Route::get('dashboard', DashboardController::class)->name('dashboard');
Route::name('home.')->prefix('home')->middleware(['auth', 'verified'])->group(function () {
    Route::resource('activities', App\Http\Controllers\Home\ActivityController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('articles', App\Http\Controllers\Home\ArticleController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('groups', App\Http\Controllers\Home\GroupController::class)->only(['show', 'edit', 'update']);
});

/*
 * ADMIN ZONE - FILAMENT STUFF
 */

// Impersonation routes (authenticated users with Filament admin access)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])->name('admin.impersonate.start');
    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';
