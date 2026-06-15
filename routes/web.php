<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BackstageController;
use App\Http\Controllers\BuildDashboardController;
use App\Http\Controllers\DemoLoginController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\StyleguideController;
use App\Http\Controllers\VolunteerController;
use App\Http\Middleware\BackstageDemoAccess;
use App\Http\Middleware\SetLocale;
use App\Livewire\Backstage\ActivityPhotoUpload;
use App\Mail\VolunteerInvite;
use App\Models\Group;
use Illuminate\Support\Facades\Route;

// Bare root → default locale.
Route::get('/', fn () => redirect('/nl', 301));

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

        // Roze-hesje page — the logged-in-only chapter surface (replaces the old backstage).
        // Lives in the public framework with a roze hero; gated on chapter membership.
        // BackstageDemoAccess keeps the demo frictionless (auto-login outside production).
        Route::get('chapters/{group}/roze-hesjes', [GroupController::class, 'rozeHesjes'])
            ->middleware(BackstageDemoAccess::class)
            ->name('groups.roze-hesjes');

        // Read-only preview of a chapter ride that is still in preparation (draft). Membership-gated,
        // like the roze page. FAUX exemplar until Activity gains a draft/lifecycle state (Nico #37).
        Route::get('chapters/{group}/rit-in-voorbereiding', [GroupController::class, 'ridePreview'])
            ->middleware(BackstageDemoAccess::class)
            ->name('groups.ride-preview');

        // Help out (J2 orientation page — lists groups so a volunteer can route to a chapter).
        Route::get('help-out', VolunteerController::class)->name('volunteer');

        // Getting started.
        Route::view('getting-started', 'getting-started')->name('getting-started');
        Route::view('find-a-bike', 'find-a-bike')->name('find-a-bike');

        // About section.
        Route::view('about', 'about.index')->name('about');
        Route::view('about/mission', 'about.mission')->name('about.mission');
        Route::view('about/vision', 'about.vision')->name('about.vision');
        Route::view('about/organisation', 'about.organisation')->name('about.organisation');
        Route::get('about/news', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('about/news/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::view('about/press', 'about.press')->name('about.press');
        Route::view('about/partners', 'about.partners')->name('about.partners');

        // Support ("Steun Kidical Mass"). Path is /steun-ons; the route name stays
        // `membership` (links use route('membership')). The old /membership path 301s
        // here so anything indexed from the old site keeps resolving.
        Route::view('steun-ons', 'steun-ons')->name('membership');
        Route::get('membership', fn (string $locale) => redirect()->route('membership', ['locale' => $locale], 301))->name('membership.legacy');

        // Contact (national).
        Route::view('contact', 'contact')->name('contact');

        // Legal / utilities. Privacy + cookies are one page; /cookies 301s to it
        // so any links indexed from the old Wix site keep resolving.
        Route::view('privacy', 'privacy')->name('privacy');
        Route::get('cookies', fn (string $locale) => redirect()->route('privacy', ['locale' => $locale], 301))->name('cookies');
    });

// Authenticated (unprefixed — deferred logged-in tier).
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Pink-vest onboarding PROTOTYPE (Mon 8 June demo, example chapter: Oudergem) ──
// Backstage: the logged-in volunteer surface for a chapter (D-1). Separate branded
// shell, not Filament. Spec: docs/superpowers/specs/2026-06-06-pink-vest-onboarding-prototype-design.md

// Account activation (stands in for the invite-token set-password step).
Route::get('activeer/{group:shortname}', [BackstageController::class, 'showActivate'])->name('backstage.activate');
Route::post('activeer/{group:shortname}', [BackstageController::class, 'activate']);

Route::middleware([BackstageDemoAccess::class])->prefix('backstage')->name('backstage.')->group(function (): void {
    Route::get('{group:shortname}', [BackstageController::class, 'home'])->name('home');
    Route::get('{group:shortname}/welkom', [BackstageController::class, 'welcome'])->name('welcome');
    Route::get('{group:shortname}/team', [BackstageController::class, 'team'])->name('team');

    Route::get('{group:shortname}/activiteit/{activity}/fotos-upload', ActivityPhotoUpload::class)
        ->name('activity.photo-upload');
});

// Invite-email preview (non-production only).
if (! app()->isProduction()) {
    Route::get('prototype/mail/uitnodiging', function () {
        $group = Group::where('shortname', 'oudergem')->firstOrFail();
        $volunteer = $group->users()->where('email', 'morgane@example.test')->firstOrFail();

        return new VolunteerInvite($volunteer, $group);
    })->name('prototype.mail.invite');
}

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

    // Internal styleguide — live component overview + extraction audit.
    Route::get('/styleguide', StyleguideController::class)
        ->name('styleguide');

    // Demo login-as shortcuts — auto-login as specific role presets (seeded by DemoUserSeeder).
    Route::get('login/as/{role}', DemoLoginController::class)
        ->name('login.as');
}
