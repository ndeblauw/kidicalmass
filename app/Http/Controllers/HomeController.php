<?php

namespace App\Http\Controllers;

use App\Support\Location\CurrentLocation;
use App\Support\Location\NextRideFinder;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** @param string $locale Supplied by the {locale} route prefix (set via SetLocale middleware); kept first for route-model binding order. */
    public function __invoke(string $locale): View
    {
        $location = CurrentLocation::resolve();
        $next = NextRideFinder::find($location);

        return view('home', [
            'hasLocation' => $location !== null,
            'nextRide' => $next['ride'],
            'nextRideDistanceKm' => $next['distance_km'],
            'nextRideIsFar' => $next['is_far'],
            'hasUpcoming' => $next['has_upcoming'],
            'upcomingRides' => $next['upcoming_preview'],
        ]);
    }
}
