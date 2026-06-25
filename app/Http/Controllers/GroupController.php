<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(string $locale): View
    {
        $groups = Group::visible()
            ->with(['parent', 'children'])
            ->withCount(['articles', 'activities'])
            ->get();

        $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
            ->get()->keyBy('zip');

        $location = CurrentLocation::resolve();

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
            'groups', 'location', 'myGroups',
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
        // Region/country nodes (Belgium, Brussels, Flanders, Wallonia) are
        // invisible grouping data, not public pages: they're excluded from the
        // index, so the detail route must refuse them too rather than leak a
        // half-built hub by direct URL.
        abort_if($group->invisible, 404);

        $group->load(['parent', 'children', 'users', 'media'])->loadCount(['articles', 'activities']);

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
            ->published()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        $upcomingRides = $activities->where('activity_type', ActivityType::KIDICALMASS)->values();
        $otherActivities = $activities->where('activity_type', '!=', ActivityType::KIDICALMASS)->values();

        $pastRidesCount = Activity::query()
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->count();

        $partners = $group->partners()->where('visible', true)->with('media')->orderBy('name')->get();
        $pressArticles = $group->pressArticles()->with('media')->latest('published_at')->get();

        // The gallery now follows the most recent ride that actually has photos
        // (group's own rides + parent regions, like the agenda above), so the page
        // always highlights the latest outing rather than a hand-curated wall.
        $latestRide = Activity::query()
            ->published()
            ->with('media')
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->first();

        return view('groups.show', compact(
            'group', 'articles', 'activities', 'partners', 'pressArticles', 'latestRide',
            'upcomingRides', 'otherActivities', 'pastRidesCount',
        ));
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
