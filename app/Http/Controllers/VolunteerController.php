<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class VolunteerController extends Controller
{
    /**
     * Help out — the J2 orientation page. The location picker is the gateway:
     * once a location cookie is set, the 4 nearest chapters are shown so a
     * volunteer can tap their own and land straight on that chapter's sign-up form.
     */
    public function __invoke(string $locale): View
    {
        $location = CurrentLocation::resolve();
        $nearestGroups = new Collection;

        if ($location) {
            $groups = Group::visible()
                ->orderBy('name')
                ->get(['id', 'name', 'zip']);

            $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
                ->get()->keyBy('zip');

            $nearestGroups = Proximity::nearest(
                $groups,
                ['lat' => $location['lat'], 'lng' => $location['lng']],
                4,
                fn ($group) => $group->zip && $coordsByZip->has($group->zip)
                    ? ['lat' => $coordsByZip[$group->zip]->latitude, 'lng' => $coordsByZip[$group->zip]->longitude]
                    : null,
            );
        }

        return view('volunteer', compact('location', 'nearestGroups'));
    }
}
