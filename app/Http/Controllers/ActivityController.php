<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Response;

class ActivityController extends Controller
{
    public function index(string $locale)
    {
        $activities = Activity::with(['author', 'groups'])
            ->orderBy('begin_date')
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    public function show(string $locale, Activity $activity)
    {
        $activity->load(['author', 'groups']);

        return view('activities.show', compact('activity'));
    }

    public function ical(string $locale, Activity $activity): Response
    {
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
