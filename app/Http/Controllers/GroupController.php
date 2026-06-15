<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(string $locale): View
    {
        $groups = Group::visible()
            ->with(['parent', 'children'])
            ->withCount(['articles', 'activities'])
            ->get();

        $activityCount = Activity::whereYear('begin_date', now()->year)->count();

        $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
            ->get()->keyBy('zip');

        $location = CurrentLocation::resolve();
        $nearby = collect();

        if ($location) {
            $partition = Proximity::partitionByRadius(
                $groups,
                ['lat' => $location['lat'], 'lng' => $location['lng']],
                (float) config('location.nearby_radius_km'),
                fn ($group) => $group->zip && $coordsByZip->has($group->zip)
                    ? ['lat' => $coordsByZip[$group->zip]->latitude, 'lng' => $coordsByZip[$group->zip]->longitude]
                    : null,
            );

            $nearby = $partition['nearby'];
        }

        $myGroups = auth()->check()
            ? auth()->user()->groups()->where('invisible', false)->get()
            : collect();

        $regionLabels = [
            'Brussels Capital Region' => 'Brussel',
            'Wallonia' => 'Wallonië',
            'Flanders' => 'Vlaanderen',
        ];

        $markers = $this->mapMarkers($groups, $coordsByZip, $regionLabels);

        $regionCounts = $groups
            ->groupBy(fn (Group $group) => $group->parent?->name)
            ->map->count();

        return view('groups.index', compact(
            'groups', 'activityCount', 'location', 'nearby', 'myGroups',
            'markers', 'regionCounts', 'regionLabels',
        ));
    }

    /**
     * @param  Collection<int, Group>  $groups
     * @param  Collection<string, PostalCode>  $coordsByZip
     * @param  array<string, string>  $regionLabels
     * @return list<array{name: string, slug: string, url: string, region: ?string, regionLabel: ?string, zip: ?string, lat: ?float, lng: ?float}>
     */
    private function mapMarkers(
        Collection $groups,
        Collection $coordsByZip,
        array $regionLabels,
    ): array {
        return $groups->map(function (Group $group) use ($coordsByZip, $regionLabels): array {
            $postalCode = $group->zip ? $coordsByZip->get($group->zip) : null;
            $region = $group->parent?->name;

            return [
                'name' => $group->name,
                'slug' => $group->shortname,
                'url' => route('groups.show', $group),
                'region' => $region,
                'regionLabel' => $region ? ($regionLabels[$region] ?? $region) : null,
                'zip' => $group->zip,
                'lat' => $postalCode?->latitude,
                'lng' => $postalCode?->longitude,
            ];
        })->values()->all();
    }

    /**
     * The "start a local group" page — the canonical entry for would-be local
     * organisers (replaces the mailto:bike@ coda on Help out + the Chapters CTA).
     * Holds the StartGroupEnquiry intent form. Out of nav, reached contextually.
     */
    public function start(string $locale): View
    {
        $groupCount = Group::visible()->count();

        return view('groups.start', compact('groupCount'));
    }

    public function show(string $locale, Group $group): View
    {
        $group->load(['parent', 'children', 'users'])->loadCount(['articles', 'activities']);

        $groupIds = collect([$group->id]);
        $currentParent = $group->parent;
        while ($currentParent) {
            $groupIds->push($currentParent->id);
            $currentParent = $currentParent->parent;
        }

        $articles = Article::query()
            ->with('author')
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->latest()
            ->get();

        $activities = Activity::query()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        return view('groups.show', compact('group', 'articles', 'activities'));
    }

    /**
     * How long the compact welcome block stays visible to a roze hesje, measured from their
     * first visit (stored in a per-group cookie). Tentative — easy to retune.
     */
    private const ROZE_WELCOME_WEEKS = 2;

    /**
     * The roze-hesje page — the logged-in-only surface for one chapter (replaces the old
     * backstage). Membership-gated: a visitor must be a roze hesje of this chapter. The full
     * roster + besloten materials are visible here, not on the public page.
     */
    public function rozeHesjes(string $locale, Group $group): View
    {
        $group->load(['users', 'children', 'parent']);

        $user = request()->user();
        abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

        // Typed upcoming agenda incl. the parent region's rides (mirrors show()).
        $groupIds = collect([$group->id]);
        $currentParent = $group->parent;
        while ($currentParent) {
            $groupIds->push($currentParent->id);
            $currentParent = $currentParent->parent;
        }

        $activities = Activity::query()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        $roster = $group->users->sortBy('name')->values();
        $lead = $activities->first()?->author ?? $roster->first();

        // A member is "nieuw" for the same window the welcome block uses (their first weeks).
        // Real data: group_user.created_at exists via withTimestamps().
        $newMemberCutoff = now()->subWeeks(self::ROZE_WELCOME_WEEKS);

        // Time-boxed welcome: show the compact welcome block only during a hesje's first weeks.
        // A per-group cookie records the first visit; after the window the block auto-hides, but
        // the permanent onboarding section keeps the same info findable. Per-browser for now;
        // a per-user flag is a later backend concern (Nico).
        $cookieKey = 'roze_welcome_'.$group->id;
        $firstSeen = request()->cookie($cookieKey);

        if ($firstSeen === null) {
            $showWelcome = true;
            // Persist well beyond the window so the block correctly hides (not resets) after it.
            Cookie::queue($cookieKey, now()->toIso8601String(), 60 * 24 * 90);
        } else {
            $showWelcome = Carbon::parse($firstSeen)
                ->greaterThan(now()->subWeeks(self::ROZE_WELCOME_WEEKS));
        }

        return view('groups.roze-hesjes', compact('group', 'activities', 'roster', 'lead', 'showWelcome', 'newMemberCutoff'));
    }

    /**
     * Read-only preview of a ride still in preparation. A hesje may look over the captains'
     * shoulder (this is the onboarding ladder: kijken → meedoen → kapitein) but cannot act.
     * FAUX exemplar — no Activity lifecycle state exists yet (Nico #37).
     */
    public function ridePreview(string $locale, Group $group): View
    {
        $user = request()->user();
        abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

        return view('groups.ride-preview', compact('group'));
    }
}
