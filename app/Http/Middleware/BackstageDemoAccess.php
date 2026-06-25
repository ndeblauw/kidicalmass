<?php

namespace App\Http\Middleware;

use App\Models\Group;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frictionless backstage for the pink-vest onboarding demo (D-1): outside production,
 * a guest landing on any backstage URL is silently signed in as a demo member of the
 * chapter so the pages just open — no login page, no password. In production (where the
 * demo members do not exist) a guest is sent to the activate screen instead.
 */
class BackstageDemoAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null) {
            return $next($request);
        }

        $group = $request->route('group');

        if (! $group instanceof Group) {
            return $next($request);
        }

        $volunteer = $this->demoMember($group);

        if ($volunteer !== null && ! app()->isProduction()) {
            Auth::login($volunteer);

            return $next($request);
        }

        return redirect()->route('backstage.activate', $group);
    }

    /**
     * Pick the member to sign in for the demo. Prefer the legacy prototype volunteer
     * (morgane), then a plain roze hesje over a captain so the hub opens in the member
     * experience, falling back to whoever is on the roster.
     */
    private function demoMember(Group $group): ?User
    {
        $members = $group->users()->get();

        return $members->firstWhere('email', 'morgane@example.test')
            ?? $members->first(fn (User $user): bool => $user->pivot->role !== 'captain')
            ?? $members->first();
    }
}
