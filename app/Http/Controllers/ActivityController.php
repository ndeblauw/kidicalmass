<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(string $locale): View
    {
        // The hero + opt-in are static; the filterable rides list is the <livewire:ride-calendar>
        // component (location-first filter + grouped agenda, rides-only per D-2/J1).
        return view('activities.index');
    }

    public function show(string $locale, Activity $activity): View
    {
        $this->authorizeAccess($activity);

        $activity->load(['author', 'groups.users']);

        // Rides get the full ride layout (route map, pace promises, pink-vest ask);
        // every other type (workshop/meeting/other) gets the lighter, description-led
        // "basic activity" page. One route, two faces — split per D-2.
        $view = $activity->activity_type->isRide() ? 'activities.show' : 'activities.show-basic';

        return view($view, compact('activity'));
    }

    private function authorizeAccess(Activity $activity): void
    {
        if ($activity->is_published) {
            return;
        }

        $user = auth()->user();

        abort_unless($user, 404);

        $isMemberOfGroup = $activity->groups()
            ->whereHas('users', fn ($q) => $q->whereKey($user))
            ->exists();

        abort_unless($isMemberOfGroup, 404);
    }

    public function ical(string $locale, Activity $activity): Response
    {
        $this->authorizeAccess($activity);
        $summary = e($activity->title_nl);
        $location = e($activity->location);
        $start = $activity->begin_date->utc()->format('Ymd\THis\Z');
        $end = ($activity->end_date ?? $activity->begin_date)->utc()->format('Ymd\THis\Z');
        $uid = $activity->id.'@kidicalmass.be';
        $url = route('activities.show', $activity);

        $ics = implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Kidical Mass//Activity//EN',
            'BEGIN:VEVENT',
            "UID:{$uid}",
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:{$summary}",
            "LOCATION:{$location}",
            "URL:{$url}",
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"activity-{$activity->id}.ics\"",
        ]);
    }
}
