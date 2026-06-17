<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $today = now()->toDateString();

            if (session('last_active_on') !== $today) {
                /** @var User $user */
                $user = Auth::user();

                if ($user->last_active_on?->toDateString() !== $today) {
                    $user->updateQuietly(['last_active_on' => $today]);
                }

                session(['last_active_on' => $today]);
            }
        }

        return $next($request);
    }
}
