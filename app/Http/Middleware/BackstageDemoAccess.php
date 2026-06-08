<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frictionless backstage for the pink-vest onboarding demo (D-1): outside production,
 * a guest landing on any backstage URL is silently signed in as the chapter's demo
 * volunteer so the pages just open — no login page, no password. In production (where
 * the demo volunteer does not exist) a guest is sent to the activate screen instead.
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

        $volunteer = $group->users()->where('email', 'morgane@example.test')->first();

        if ($volunteer !== null && ! app()->isProduction()) {
            Auth::login($volunteer);

            return $next($request);
        }

        return redirect()->route('backstage.activate', $group);
    }
}
