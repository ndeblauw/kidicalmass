<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::visible()
            ->with(['parent', 'children'])
            ->withCount(['articles', 'activities'])
            ->get();

        return view('groups.index', compact('groups'));
    }

    public function show(Group $group)
    {
        $group->load(['parent', 'children', 'users']);

        // Get direct articles and activities
        $articles = $group->articles()->with('author')->get();
        $activities = $group->activities()->with('author')->get();

        // Get inherited articles and activities from parent groups
        $inheritedArticles = collect();
        $inheritedActivities = collect();

        $currentParent = $group->parent;
        while ($currentParent) {
            $inheritedArticles = $inheritedArticles->merge(
                $currentParent->articles()->with('author')->get()
            );
            $inheritedActivities = $inheritedActivities->merge(
                $currentParent->activities()->with('author')->get()
            );
            $currentParent = $currentParent->parent;
        }

        return view('groups.show', compact(
            'group',
            'articles',
            'activities',
            'inheritedArticles',
            'inheritedActivities'
        ));
    }
}
