<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Support\RideDate;
use App\Support\RozeHub\OverviewMoment;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

/**
 * The roze-hesje hub for one chapter: a logged-in, membership-gated section that
 * lives inside the public site. One Overview page plus 5 sub-pages, all sharing the
 * compact pink hero + sub-nav chrome (<x-roze-hub>). Backend for the feed, galleries,
 * draft-state and per-group links is faux/seeded for now (Nico, GitHub #37).
 */
class RozeHesjeController extends Controller
{
    /** How long the welcome block + "nieuw" marker stay visible, from first visit. */
    private const ROZE_WELCOME_WEEKS = 2;

    public function overview(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

        // The next published ride (own chapter + its region/country lineage) anchors
        // the front door with something live every visit, not just the welcome block.
        $nextRide = Activity::query()
            ->published()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $this->lineageIds($group)))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->first();

        // The Monday-after moment: the chapter's own most recent ride, at most
        // RECAP_DAYS old, that already has album photos. No photos = no recap
        // (the feed's photo card still covers late uploads). Stateless — no cookies.
        $recapRide = Activity::query()
            ->published()
            ->with('media')
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->whereBetween('begin_date', [now()->subDays(OverviewMoment::RECAP_DAYS), now()])
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->first();

        return view('groups.roze-hesjes.overzicht', [
            ...$context,
            'nextRide' => $nextRide,
            'recapRide' => $recapRide,
            'moment' => OverviewMoment::resolve($context['showWelcome'], $recapRide, $nextRide),
            'countdown' => $nextRide ? OverviewMoment::countdownLabel($nextRide) : null,
            'feed' => $this->feed($group, $nextRide),
        ]);
    }

    public function aanDeSlag(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.aan-de-slag', $this->hubContext($group));
    }

    public function agenda(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

        // Confirmed rides follow the chapter's lineage (own chapter + the region/country
        // editions above it), exactly like the public page.
        $confirmed = Activity::query()
            ->published()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $this->lineageIds($group)))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        // Drafts are this chapter's own work-in-progress — never the region's — so a hesje
        // sees what their captains are preparing, not unrelated regional drafts.
        $drafts = Activity::query()
            ->drafts()
            ->with('groups')
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        return view('groups.roze-hesjes.agenda', [
            ...$context,
            'confirmed' => $confirmed,
            'drafts' => $drafts,
        ]);
    }

    public function fotos(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

        // One album per past ride that actually has photos, newest first. The view shows
        // the latest by default and lets a hesje page back to earlier outings.
        $rides = Activity::query()
            ->with('media')
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->get();

        return view('groups.roze-hesjes.fotos', [
            ...$context,
            'rides' => $rides,
        ]);
    }

    public function groep(string $locale, Group $group): View
    {
        // Everyone is visible to fellow hesjes, but ordered and labelled honestly:
        // captains lead, then pink vests who ride, then interested members (no role
        // yet) — each alphabetical, so a newcomer finds the lead first.
        $rank = ['captain' => '0', 'pinkvest' => '1'];

        return view('groups.roze-hesjes.groep', [
            ...$this->hubContext($group),
            'roster' => $group->users
                ->sortBy(fn ($member) => ($rank[$member->pivot->role] ?? '2').'_'.$member->name)
                ->values(),
            'newMemberCutoff' => now()->subWeeks(self::ROZE_WELCOME_WEEKS),
        ]);
    }

    public function materiaal(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.materiaal', $this->hubContext($group));
    }

    /**
     * Shared chrome data + membership guard for every hub page.
     *
     * @return array{group: Group, isCaptain: bool, showWelcome: bool, beheerUrl: string}
     */
    private function hubContext(Group $group): array
    {
        $group->load(['users', 'children', 'parent']);

        $user = request()->user();
        abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

        $membership = $group->users->firstWhere('id', $user->id);
        $isCaptain = $membership?->pivot->role === 'captain';

        // Time-boxed welcome: shown only during a hesje's first weeks. A per-group cookie
        // records the first visit; after the window the block auto-hides (does not reset).
        // Per-browser for now; a per-user flag is a later backend concern (Nico).
        $cookieKey = 'roze_welcome_'.$group->id;
        $firstSeen = request()->cookie($cookieKey);

        if ($firstSeen === null) {
            $showWelcome = true;
            Cookie::queue($cookieKey, now()->toIso8601String(), 60 * 24 * 90);
        } else {
            $showWelcome = Carbon::parse($firstSeen)->greaterThan(now()->subWeeks(self::ROZE_WELCOME_WEEKS));
        }

        // Beheer leaves the hub for the Filament panel. Panel root for now; the exact
        // group-edit deep-link is a later backend concern (Nico #37).
        $beheerUrl = url('/admin');

        return compact('group', 'isCaptain', 'showWelcome', 'beheerUrl');
    }

    /** The chapter's id plus every region/country node above it, nearest first. */
    private function lineageIds(Group $group): Collection
    {
        $ids = collect([$group->id]);
        $parent = $group->parent;
        while ($parent) {
            $ids->push($parent->id);
            $parent = $parent->parent;
        }

        return $ids;
    }

    /**
     * Change-feed (newest first), each card deep-linking to its exact target. The events
     * themselves are derived from real seeded data — the latest album, the newest draft,
     * the newest hesje — rather than hard-coded; a structured change-feed with precise
     * timestamps is still Nico's (GitHub #37), so the relative labels stay approximate.
     *
     * @return array<int, array{color: string, icon: string, what: string, context: string, timestamp: string, relative: string, href: string, celebrate: bool}>
     */
    private function feed(Group $group, ?Activity $nextRide = null): array
    {
        $items = collect();

        $latestAlbum = Activity::query()
            ->with('media')
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->first();

        if ($latestAlbum) {
            $count = $latestAlbum->getMedia('gallery')->count();
            $rideDate = $latestAlbum->begin_date->locale(app()->getLocale())->isoFormat('D MMMM');
            $items->push([
                'color' => 'blue',
                'icon' => 'image',
                'what' => "{$count} foto's van de rit van {$rideDate}",
                'context' => 'Nieuw in het album',
                'timestamp' => $latestAlbum->begin_date->toDateString(),
                'relative' => $latestAlbum->begin_date->diffForHumans(),
                'href' => route('groups.roze-hesjes.fotos', [$group, 'ride' => $latestAlbum->id]),
                'celebrate' => false,
            ]);
        }

        $draft = Activity::query()
            ->drafts()
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->first();

        if ($draft) {
            $items->push([
                'color' => 'orange',
                'icon' => 'pencil',
                'what' => "{$draft->title} krijgt vorm",
                'context' => 'Rit in voorbereiding',
                'timestamp' => now()->toDateString(),
                'relative' => 'deze week',
                'href' => route('groups.ride-preview', [$group, 'ride' => $draft->id]),
                'celebrate' => false,
            ]);
        }

        $newMember = $group->users
            ->filter(fn ($member) => $member->pivot->created_at?->greaterThan(now()->subWeeks(self::ROZE_WELCOME_WEEKS)))
            ->sortByDesc(fn ($member) => $member->pivot->created_at)
            ->first();

        if ($newMember) {
            // A joining hesje is the feed's one celebration; the hello nudge only
            // appears when there is an actual ride to say hello at (rides only,
            // and never a nudge toward a vergadering).
            $what = "{$newMember->name} rijdt nu mee als roze hesje";
            if ($nextRide !== null && $nextRide->activity_type->isRide()) {
                $weekday = RideDate::weekday($nextRide->begin_date);
                $what .= ". Zeg {$weekday} zeker hallo.";
            }

            $items->push([
                'color' => 'red',
                'icon' => 'user-plus',
                'what' => $what,
                'context' => 'Nieuw lid',
                'timestamp' => $newMember->pivot->created_at->toDateString(),
                'relative' => $newMember->pivot->created_at->diffForHumans(),
                'href' => route('groups.roze-hesjes.groep', $group),
                'celebrate' => true,
            ]);
        }

        return $items->all();
    }
}
