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
        abort_if(! $activity->is_published, 404);

        $activity->load(['author', 'groups']);

        return view('activities.show', compact('activity'));
    }

    public function ical(string $locale, Activity $activity): Response
    {
        abort_if(! $activity->is_published, 404);
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
