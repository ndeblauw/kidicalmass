<?php

use App\Actions\GroupChangesResult;
use App\Enums\PartnerCategory;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ContactFormController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PressArticleController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\TeamMemberController;
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
use App\Livewire\BuildReview;
use App\Mail\VolunteerInvite;
use App\Models\Group;
use App\Models\Partner;
use App\Models\PressArticle;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\PinkVest\WelcomeNotification;
use App\Support\Quotes;
use App\Support\SupportStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Bare root → the visitor's preferred browser language (Dutch fallback). A 302
// (not 301) plus Vary, since the target depends on Accept-Language.
Route::get('/', function (Request $request) {
    $locale = SetLocale::detectFromRequest($request);

    return redirect()
        ->to(localized_route('home', ['locale' => $locale]), 302)
        ->header('Vary', 'Accept-Language');
});
Route::middleware('setlocale')->group(function (): void {
    Route::get('{locale}', HomeController::class)->where('locale', 'nl')->name('home');
    Route::get('fr', HomeController::class)->defaults('locale', 'fr')->name('fr.home');

    // Events (Activity model — "Events" is the public name for the rides calendar).
    Route::get('{locale}/events', [ActivityController::class, 'index'])->where('locale', 'nl')->name('activities.index');
    Route::get('fr/agenda', [ActivityController::class, 'index'])->defaults('locale', 'fr')->name('fr.activities.index');
    Route::get('{locale}/events/{activity}', [ActivityController::class, 'show'])->where('locale', 'nl')->name('activities.show');
    Route::get('{locale}/agenda/{activity}', [ActivityController::class, 'show'])->where('locale', 'fr')->name('fr.activities.show');
    Route::get('{locale}/events/{activity}/ical', [ActivityController::class, 'ical'])->where('locale', 'nl')->name('activities.ical');
    Route::get('{locale}/agenda/{activity}/ical', [ActivityController::class, 'ical'])->where('locale', 'fr')->name('fr.activities.ical');

    // Chapters (Group model).
    Route::get('{locale}/chapters', [GroupController::class, 'index'])->where('locale', 'nl')->name('groups.index');
    Route::get('fr/groupes-locaux', [GroupController::class, 'index'])->defaults('locale', 'fr')->name('fr.groups.index');
    // Start-a-group — must precede chapters/{group} so the wildcard binding
    // doesn't try to resolve "start-een-groep" as a chapter shortname.
    Route::get('{locale}/chapters/start-een-groep', [GroupController::class, 'start'])->where('locale', 'nl')->name('groups.start');
    Route::get('fr/groupes-locaux/creer-un-groupe', [GroupController::class, 'start'])->defaults('locale', 'fr')->name('fr.groups.start');
    Route::get('{locale}/chapters/{group}', [GroupController::class, 'show'])->where('locale', 'nl')->name('groups.show');
    Route::get('{locale}/groupes-locaux/{group}', [GroupController::class, 'show'])->where('locale', 'fr')->name('fr.groups.show');

    // Roze-hesje hub — the logged-in-only chapter section (replaces the old backstage).
    // Lives in the public framework with a compact roze hero + sub-nav; gated on chapter
    // membership. BackstageDemoAccess keeps the demo frictionless (auto-login outside prod).
    Route::middleware(BackstageDemoAccess::class)->group(function (): void {
        Route::get('{locale}/chapters/{group}/roze-hesjes', [RozeHesjeController::class, 'overview'])->where('locale', 'nl')->name('groups.roze-hesjes');
        Route::get('{locale}/chapters/{group}/roze-hesjes/aan-de-slag', [RozeHesjeController::class, 'aanDeSlag'])->where('locale', 'nl')->name('groups.roze-hesjes.aan-de-slag');
        Route::get('{locale}/chapters/{group}/roze-hesjes/agenda', [RozeHesjeController::class, 'agenda'])->where('locale', 'nl')->name('groups.roze-hesjes.agenda');
        Route::get('{locale}/chapters/{group}/roze-hesjes/fotos', [RozeHesjeController::class, 'fotos'])->where('locale', 'nl')->name('groups.roze-hesjes.fotos');
        Route::get('{locale}/chapters/{group}/roze-hesjes/groep', [RozeHesjeController::class, 'groep'])->where('locale', 'nl')->name('groups.roze-hesjes.groep');
        Route::get('{locale}/chapters/{group}/roze-hesjes/materiaal', [RozeHesjeController::class, 'materiaal'])->where('locale', 'nl')->name('groups.roze-hesjes.materiaal');
    });

    // Read-only preview of a chapter ride that is still in preparation (draft). Membership-gated,
    // like the roze page. FAUX exemplar until Activity gains a draft/lifecycle state (Nico #37).
    Route::get('{locale}/chapters/{group}/rit-in-voorbereiding', [GroupController::class, 'ridePreview'])
        ->where('locale', 'nl')
        ->middleware(BackstageDemoAccess::class)
        ->name('groups.ride-preview');

    // Help out (J2 orientation page — lists groups so a volunteer can route to a chapter).
    Route::get('{locale}/help-out', VolunteerController::class)->where('locale', 'nl')->name('volunteer');
    Route::get('fr/donner-un-coup-de-main', VolunteerController::class)->defaults('locale', 'fr')->name('fr.volunteer');

    // Getting started.
    Route::view('{locale}/getting-started', 'getting-started')->where('locale', 'nl')->name('getting-started');
    Route::view('fr/premiere-fois', 'getting-started')->defaults('locale', 'fr')->name('fr.getting-started');

    // Newsletter.
    Route::view('{locale}/nieuwsbrief', 'nieuwsbrief')->where('locale', 'nl')->name('newsletter.show');
    Route::view('fr/newsletter', 'nieuwsbrief')->defaults('locale', 'fr')->name('fr.newsletter.show');
    Route::view('{locale}/nieuwsbrief/bevestigd', 'newsletter.confirmed')->where('locale', 'nl')->name('newsletter.confirmed');
    Route::view('fr/newsletter/confirmee', 'newsletter.confirmed')->defaults('locale', 'fr')->name('fr.newsletter.confirmed');

    // About section.
    Route::view('{locale}/about', 'about.index')->where('locale', 'nl')->name('about');
    Route::view('fr/a-propos', 'about.index')->defaults('locale', 'fr')->name('fr.about');
    Route::get('{locale}/about/mission', fn (Quotes $quotes) => view('about.mission', [
        'missionQuote' => $quotes->forSlot('mission'),
    ]))->where('locale', 'nl')->name('about.mission');
    Route::get('fr/a-propos/notre-mission', fn (Quotes $quotes) => view('about.mission', [
        'missionQuote' => $quotes->forSlot('mission'),
    ]))->defaults('locale', 'fr')->name('fr.about.mission');
    Route::get('{locale}/about/vision', fn (Quotes $quotes) => view('about.vision', [
        'visionQuote1' => $quotes->forSlot('vision-1'),
        'visionQuote2' => $quotes->forSlot('vision-2'),
    ]))->where('locale', 'nl')->name('about.vision');
    Route::get('fr/a-propos/nos-revendications', fn (Quotes $quotes) => view('about.vision', [
        'visionQuote1' => $quotes->forSlot('vision-1'),
        'visionQuote2' => $quotes->forSlot('vision-2'),
    ]))->defaults('locale', 'fr')->name('fr.about.vision');
    Route::get('{locale}/about/organisation', fn () => view('about.organisation', [
        'teamMembers' => TeamMember::query()->where('visible', true)->orderBy('sort')->with('media')->get(),
    ]))->where('locale', 'nl')->name('about.organisation');
    Route::get('fr/a-propos/comment-nous-fonctionnons', fn () => view('about.organisation', [
        'teamMembers' => TeamMember::query()->where('visible', true)->orderBy('sort')->with('media')->get(),
    ]))->defaults('locale', 'fr')->name('fr.about.organisation');
    Route::get('{locale}/about/news', [ArticleController::class, 'index'])->where('locale', 'nl')->name('articles.index');
    Route::get('fr/a-propos/actualites', [ArticleController::class, 'index'])->defaults('locale', 'fr')->name('fr.articles.index');
    Route::get('{locale}/about/news/{article}', [ArticleController::class, 'show'])->where('locale', 'nl')->name('articles.show');
    Route::get('{locale}/a-propos/actualites/{article}', [ArticleController::class, 'show'])->where('locale', 'fr')->name('fr.articles.show');
    Route::get('{locale}/about/press', function () {
        $articles = PressArticle::query()
            ->whereNotNull('published_at')
            ->with('media')
            ->orderBy('published_at', 'desc')
            ->get()
            ->groupBy(fn ($article) => $article->published_at->year);

        return view('about.press', ['articlesByYear' => $articles]);
    })->where('locale', 'nl')->name('about.press');
    Route::get('fr/a-propos/presse', function () {
        $articles = PressArticle::query()
            ->whereNotNull('published_at')
            ->with('media')
            ->orderBy('published_at', 'desc')
            ->get()
            ->groupBy(fn ($article) => $article->published_at->year);

        return view('about.press', ['articlesByYear' => $articles]);
    })->defaults('locale', 'fr')->name('fr.about.press');
    Route::get('{locale}/about/partners', function () {
        $categoryOrder = [
            PartnerCategory::INSTITUTIONEEL->value,
            PartnerCategory::BONDGENOOT->value,
        ];

        $partners = Partner::query()
            ->whereNull('group_id')
            ->where('visible', true)
            ->whereIn('category', $categoryOrder)
            ->get()
            ->sortBy([
                fn ($a, $b) => array_search($a->category->value, $categoryOrder) <=> array_search($b->category->value, $categoryOrder),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]);

        return view('about.partners', ['partners' => $partners]);
    })->where('locale', 'nl')->name('about.partners');
    Route::get('fr/a-propos/partenaires', function () {
        $categoryOrder = [
            PartnerCategory::INSTITUTIONEEL->value,
            PartnerCategory::BONDGENOOT->value,
        ];

        $partners = Partner::query()
            ->whereNull('group_id')
            ->where('visible', true)
            ->whereIn('category', $categoryOrder)
            ->get()
            ->sortBy([
                fn ($a, $b) => array_search($a->category->value, $categoryOrder) <=> array_search($b->category->value, $categoryOrder),
                fn ($a, $b) => strcmp($a->name, $b->name),
            ]);

        return view('about.partners', ['partners' => $partners]);
    })->defaults('locale', 'fr')->name('fr.about.partners');

    // Support ("Steun Kidical Mass"). Path is /steun-ons; the route name stays
    // `membership` (links use route('membership')). The old /membership path 301s
    // here so anything indexed from the old site keeps resolving.
    Route::get('{locale}/steun-ons', fn () => view('steun-ons', [
        'proofCards' => (new SupportStats)->cards(),
    ]))->where('locale', 'nl')->name('membership');
    Route::get('fr/nous-soutenir', fn () => view('steun-ons', [
        'proofCards' => (new SupportStats)->cards(),
    ]))->defaults('locale', 'fr')->name('fr.membership');
    Route::get('{locale}/membership', fn (string $locale) => redirect()->to(localized_route('membership', ['locale' => $locale]), 301))
        ->where('locale', 'nl')
        ->name('membership.legacy');

    // Contact (national).
    Route::view('{locale}/contact', 'contact')->where('locale', 'nl')->name('contact');
    Route::view('fr/contact', 'contact')->defaults('locale', 'fr')->name('fr.contact');

    // Legal / utilities. Privacy + cookies are one page; /cookies 301s to it
    // so any links indexed from the old Wix site keep resolving.
    Route::view('{locale}/privacy', 'privacy')->where('locale', 'nl')->name('privacy');
    Route::view('fr/confidentialite', 'privacy')->defaults('locale', 'fr')->name('fr.privacy');
    Route::get('{locale}/cookies', fn (string $locale) => redirect()->to(localized_route('privacy', ['locale' => $locale]), 301))
        ->where('locale', 'nl')
        ->name('cookies');
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

    Route::resource('yearstats', YearStatController::class);

    Route::resource('contactforms', ContactFormController::class)
        ->only(['index', 'show', 'destroy']);

    Route::post('contactforms/{contactform}/convert-to-user', [ContactFormController::class, 'convertToUser'])
        ->name('contactforms.convert-to-user');

    Route::resource('users', UserController::class);

    Route::resource('partners', PartnerController::class);

    Route::resource('teammembers', TeamMemberController::class);

    Route::resource('quotes', QuoteController::class);

    Route::resource('groups', AdminGroupController::class);

    Route::resource('articles', AdminArticleController::class);

    Route::resource('pressarticles', PressArticleController::class);

    Route::resource('activities', AdminActivityController::class);
});

require __DIR__.'/settings.php';

// Internal build-status dashboard — non-production only, unlinked (no nav/sitemap).
if (! app()->isProduction()) {
    Route::get('/build', BuildDashboardController::class)
        ->name('build.dashboard');

    // Split review mode — walk the P-nn rows, bump statuses, drop feedback.
    Route::get('/build/review/{pageId?}', BuildReview::class)
        ->name('build.review');

    // Internal styleguide — live component overview + extraction audit.
    Route::get('/styleguide', StyleguideController::class)
        ->name('styleguide');

    // Demo login-as shortcuts — auto-login as specific role presets (seeded by DemoUserSeeder).
    Route::get('login/as/{role}', DemoLoginController::class)
        ->name('login.as');

    // Error-page previews — 500/503 can't be reached by URL otherwise.
    Route::get('preview/errors/{code}', function (string $code) {
        abort_unless(in_array($code, ['404', '403', '419', '500', '503'], true), 404);

        return response()->view('errors.'.$code, [], (int) $code);
    })->name('preview.errors');
}

// Legacy Wix URLs → 301s to the new site (docs/wiki/design/26-redirect-map.md, D-7).
require __DIR__.'/redirects.php';
