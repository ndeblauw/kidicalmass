<?php

use App\Actions\GroupChangesResult;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\ContactFormController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\YearStatController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BackstageController;
use App\Http\Controllers\BuildDashboardController;
use App\Http\Controllers\DemoLoginController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\RozeHesjeController;
use App\Http\Controllers\StyleguideController;
use App\Http\Controllers\VolunteerController;
use App\Http\Middleware\BackstageDemoAccess;
use App\Http\Middleware\SetLocale;
use App\Livewire\Backstage\ActivityPhotoUpload;
use App\Mail\VolunteerInvite;
use App\Models\Article;
use App\Models\Group;
use App\Models\PressArticle;
use App\Models\User;
use App\Notifications\PinkVest\WelcomeNotification;
use App\Support\SupportStats;
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
        // Start-a-group — must precede chapters/{group} so the wildcard binding
        // doesn't try to resolve "start-een-groep" as a chapter shortname.
        Route::get('chapters/start-een-groep', [GroupController::class, 'start'])->name('groups.start');
        Route::get('chapters/{group}', [GroupController::class, 'show'])->name('groups.show');

        // Roze-hesje hub — the logged-in-only chapter section (replaces the old backstage).
        // Lives in the public framework with a compact roze hero + sub-nav; gated on chapter
        // membership. BackstageDemoAccess keeps the demo frictionless (auto-login outside prod).
        Route::middleware(BackstageDemoAccess::class)->group(function (): void {
            Route::get('chapters/{group}/roze-hesjes', [RozeHesjeController::class, 'overview'])->name('groups.roze-hesjes');
            Route::get('chapters/{group}/roze-hesjes/aan-de-slag', [RozeHesjeController::class, 'aanDeSlag'])->name('groups.roze-hesjes.aan-de-slag');
            Route::get('chapters/{group}/roze-hesjes/agenda', [RozeHesjeController::class, 'agenda'])->name('groups.roze-hesjes.agenda');
            Route::get('chapters/{group}/roze-hesjes/fotos', [RozeHesjeController::class, 'fotos'])->name('groups.roze-hesjes.fotos');
            Route::get('chapters/{group}/roze-hesjes/groep', [RozeHesjeController::class, 'groep'])->name('groups.roze-hesjes.groep');
            Route::get('chapters/{group}/roze-hesjes/materiaal', [RozeHesjeController::class, 'materiaal'])->name('groups.roze-hesjes.materiaal');
        });

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

        // Newsletter.
        Route::view('nieuwsbrief', 'nieuwsbrief')->name('newsletter.show');
        Route::view('nieuwsbrief/bevestigd', 'newsletter.confirmed')->name('newsletter.confirmed');

        // About section.
        Route::view('about', 'about.index')->name('about');
        Route::view('about/mission', 'about.mission')->name('about.mission');
        Route::view('about/vision', 'about.vision')->name('about.vision');
        Route::view('about/organisation', 'about.organisation')->name('about.organisation');
        Route::get('about/news', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('about/news/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('about/press', function () {
            $articles = PressArticle::query()
                ->whereNotNull('published_at')
                ->orderBy('published_at', 'desc')
                ->get()
                ->groupBy(fn ($article) => $article->published_at->year);

            return view('about.press', ['articlesByYear' => $articles]);
        })->name('about.press');
        Route::view('about/partners', 'about.partners')->name('about.partners');

        // Support ("Steun Kidical Mass"). Path is /steun-ons; the route name stays
        // `membership` (links use route('membership')). The old /membership path 301s
        // here so anything indexed from the old site keeps resolving.
        Route::get('steun-ons', fn () => view('steun-ons', [
            'proofCards' => (new SupportStats)->cards(),
        ]))->name('membership');
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
        $volunteer = $group->users()->firstOrFail();

        return new VolunteerInvite($volunteer, $group);
    })->name('prototype.mail.invite');

    Route::get('prototype/mail/welkom-roze-hesje', function () {
        // $group = Group::where('shortname', 'oudergem')->firstOrFail();
        $volunteer = User::where('email', 'pinkvest@kidi.be')->firstOrFail();
        $group = $volunteer->groups()->firstOrFail();

        $volunteer->notify(new WelcomeNotification($group));

        return (new WelcomeNotification($group))->toMail($volunteer);
    })->name('prototype.mail.welcome');

    // Monthly group-update digest (J1 #6). Demo group: Schaarbeek. Real recap rides
    // and upcoming activities; faux pink vests + article so every block is visible.
    Route::get('prototype/mail/groep-update', function () {
        $group = Group::where('name', 'Schaarbeek')->firstOrFail();

        $recentRidesWithPhotos = $group->activities()
            ->where('begin_date', '<', now())
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->get();

        $upcomingActivities = $group->activities()
            ->where('published', true)
            ->whereBetween('begin_date', [now(), now()->addMonths(3)])
            ->orderBy('begin_date')
            ->get();

        $pinkVests = collect(['Sofie Maes', 'Mehmet Yilmaz', 'Lars De Smet'])
            ->map(fn (string $name) => (new User)->forceFill(['name' => $name]));

        $article = (new Article)->forceFill([
            'title_nl' => 'Een massa kets kleurt de Haachtsesteenweg',
            'content_nl' => 'De buurt liep uit voor de lenterit: muziek, bakfietsen en kinderen die de straat even helemaal voor zich hadden. De pers pikte het op.',
        ]);

        $result = new GroupChangesResult(
            startDate: now()->subMonth(),
            endDate: now(),
            group: $group,
            newActivities: collect(),
            updatedActivities: collect(),
            newCaptains: collect(),
            newPinkVests: $pinkVests,
            newInterested: collect(),
            newArticles: collect([$article]),
            updatedArticles: collect(),
            recentRidesWithPhotos: $recentRidesWithPhotos,
            upcomingActivities: $upcomingActivities,
        );

        return view('emails.group-update', ['changes' => collect([$result])]);
    })->name('prototype.mail.group-update');
}

Route::middleware(['auth'])->prefix('admin')->group(function (): void {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    Route::resource('year-stats', YearStatController::class);

    Route::resource('contact-forms', ContactFormController::class)
        ->only(['index', 'show', 'destroy']);

    Route::resource('users', UserController::class);

    Route::resource('partners', PartnerController::class);
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
