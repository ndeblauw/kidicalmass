<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::resource('articles', ArticleController::class)->only(['index', 'show']);
Route::resource('activities', ActivityController::class)->only(['index', 'show']);
Route::get('activities/{activity}/ical', [ActivityController::class, 'ical'])->name('activities.ical');
Route::resource('groups', GroupController::class)->only(['index', 'show']);
Route::view('/vrijwilliger', 'volunteer')->name('volunteer');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Impersonation routes (authenticated users with Filament admin access)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';
