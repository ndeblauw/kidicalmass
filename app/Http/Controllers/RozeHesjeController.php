<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
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
        return view('groups.roze-hesjes.overzicht', [
            ...$this->hubContext($group),
            'feed' => $this->fauxFeed($group),
        ]);
    }

    public function aanDeSlag(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.aan-de-slag', $this->hubContext($group));
    }

    public function agenda(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

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

        $lead = $activities->first()?->author ?? $group->users->sortBy('name')->first();

        return view('groups.roze-hesjes.agenda', [
            ...$context,
            'activities' => $activities,
            'lead' => $lead,
        ]);
    }

    public function fotos(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.fotos', $this->hubContext($group));
    }

    public function groep(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.groep', [
            ...$this->hubContext($group),
            'roster' => $group->users->sortBy('name')->values(),
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

    /**
     * Faux change-feed (newest first). Each item deep-links to its exact target.
     * Real records come from the change-feed Nico builds (GitHub #37).
     *
     * @return array<int, array{type: string, color: string, icon: string, what: string, context: string, timestamp: string, relative: string, href: string}>
     */
    private function fauxFeed(Group $group): array
    {
        return [
            [
                'type' => 'photos',
                'color' => 'blue',
                'icon' => 'image',
                'what' => "3 nieuwe foto's van de rit van zondag",
                'context' => 'Rit van zondag',
                'timestamp' => now()->subDays(2)->toDateString(),
                'relative' => '2 dagen geleden',
                'href' => route('groups.roze-hesjes.fotos', $group),
            ],
            [
                'type' => 'draft',
                'color' => 'orange',
                'icon' => 'pencil',
                'what' => 'De Halloweenrit krijgt vorm',
                'context' => 'Route gewijzigd',
                'timestamp' => now()->subDays(3)->toDateString(),
                'relative' => '3 dagen geleden',
                'href' => route('groups.ride-preview', $group),
            ],
            [
                'type' => 'member',
                'color' => 'red',
                'icon' => 'user-plus',
                'what' => 'Sara rijdt nu mee als roze hesje',
                'context' => 'Nieuw lid',
                'timestamp' => now()->subDays(5)->toDateString(),
                'relative' => '5 dagen geleden',
                'href' => route('groups.roze-hesjes.groep', $group),
            ],
        ];
    }
}
