<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\View\View;

class VolunteerController extends Controller
{
    /**
     * Help out — the J2 orientation page. Lists the visible groups so a motivated
     * volunteer can tap their own and land straight on that chapter's sign-up form
     * (the choice is the CTA; no map detour, no typing a municipality).
     */
    public function __invoke(string $locale): View
    {
        $groups = Group::visible()
            ->orderBy('name')
            ->get(['id', 'name', 'zip']);

        return view('volunteer', compact('groups'));
    }
}
