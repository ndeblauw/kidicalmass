<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Backstage — the logged-in volunteer surface for a chapter (D-1).
 *
 * Prototype scope (Mon 8 June demo): the first-login welcome, the material-library
 * home, and the volunteer roster, plus a shallow "activate your account" step that
 * stands in for real invite-token provisioning. Example chapter: Oudergem.
 *
 * See docs/superpowers/specs/2026-06-06-pink-vest-onboarding-prototype-design.md
 */
class BackstageController extends Controller
{
    /** First-login welcome: "klaar voor je eerste rit". */
    public function welcome(Group $group): View
    {
        return view('backstage.welcome', $this->chapterData($group));
    }

    /** Material-library home — the standing landing after onboarding. */
    public function home(Group $group): View
    {
        return view('backstage.home', $this->chapterData($group));
    }

    /** Volunteer roster — who else rides in this chapter (logged-in only). */
    public function team(Group $group): View
    {
        return view('backstage.team', $this->chapterData($group));
    }

    /**
     * Shared chapter context: the volunteer (current user), the chapter lead
     * (heuristic: author of the chapter's activities), the roster, and what's coming up.
     *
     * @return array{group: Group, volunteer: ?User, lead: ?User, roster: Collection<int, User>, upcoming: Collection<int, Activity>}
     */
    private function chapterData(Group $group): array
    {
        $group->load(['users', 'activities.author']);

        $upcoming = $group->activities()
            ->with('author')
            ->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')
            ->get();

        $roster = $group->users->sortBy('name')->values();
        $lead = $upcoming->first()?->author ?? $roster->first();

        return [
            'group' => $group,
            'volunteer' => request()->user() ?? $roster->firstWhere('email', 'morgane@example.test') ?? $roster->first(),
            'lead' => $lead,
            'roster' => $roster,
            'upcoming' => $upcoming,
        ];
    }

    /** Account-activation screen (stands in for the invite-token set-password step). */
    public function showActivate(Group $group): View
    {
        $volunteer = $group->users()->where('email', 'morgane@example.test')->first();

        return view('auth.activate', ['group' => $group, 'volunteer' => $volunteer]);
    }

    /**
     * One-click activation for the demo: log the new pink vest in and drop them on the
     * welcome screen. No password — the prototype is about showing the surfaces, not
     * real auth. Real invite-token provisioning is out of scope (D-12 hand-off).
     */
    public function activate(Group $group, StatefulGuard $guard): RedirectResponse
    {
        $volunteer = $group->users()->where('email', 'morgane@example.test')->firstOrFail();
        $guard->login($volunteer);

        return redirect()->route('backstage.welcome', $group);
    }
}
