<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BuildDashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Bare root → default locale.
Route::get('/', fn () => redirect('/nl'));

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::SUPPORTED)
    ->middleware('setlocale')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        // Events (Activity model — "Events" is the public name for the rides calendar).
        Route::get('events', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('events/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::get('events/{activity}/ical', [ActivityController::class, 'ical'])->name('activities.ical');

        // Chapters (Group model).
        Route::get('chapters', [GroupController::class, 'index'])->name('groups.index');
        Route::get('chapters/{group}', [GroupController::class, 'show'])->name('groups.show');

        // Help out.
        Route::view('help-out', 'volunteer')->name('volunteer');

        // Getting started.
        Route::view('getting-started', 'getting-started')->name('getting-started');

        // About section.
        Route::view('about', 'about.index')->name('about');
        Route::view('about/mission', 'about.mission')->name('about.mission');
        Route::view('about/vision', 'about.vision')->name('about.vision');
        Route::view('about/organisation', 'about.organisation')->name('about.organisation');
        Route::get('about/news', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('about/news/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::view('about/press', 'about.press')->name('about.press');
        Route::view('about/partners', 'about.partners')->name('about.partners');

        // Membership.
        Route::view('membership', 'membership')->name('membership');

        // Contact (national).
        Route::view('contact', 'contact')->name('contact');

        // Legal / utilities.
        Route::view('privacy', 'privacy')->name('privacy');
        Route::view('cookies', 'cookies')->name('cookies');
    });

// Authenticated (unprefixed — deferred logged-in tier).
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->group(function (): void {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';

// Internal build-status dashboard — non-production only, unlinked (no nav/sitemap).
if (! app()->isProduction()) {
    Route::get('/build', BuildDashboardController::class)
        ->name('build.dashboard');
}
