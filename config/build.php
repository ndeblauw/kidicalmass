<?php

return [
    // Wiki sources (relative to base_path). The markdown is the source of truth;
    // the /build dashboard only parses it.
    'sources' => [
        'structure' => 'docs/wiki/design/20-structure.md',
        'skeleton' => 'docs/wiki/design/30-skeleton/00-page-registry.md',
        'concerns' => 'docs/wiki/design/01-concerns.md',
        'patterns' => 'docs/wiki/design/40-patterns.md',
    ],

    // Per-page UX brief file (relative to base_path), keyed by registry ID.
    // Drives the "UX declared but brief missing" drift check.
    'briefs' => [
        'P-01' => 'docs/wiki/design/30-skeleton/home.md',
        'P-02' => 'docs/wiki/design/30-skeleton/events-overview.md',
        'P-03' => 'docs/wiki/design/30-skeleton/activity-detail.md',
        'P-04' => 'docs/wiki/design/30-skeleton/membership.md',
        'P-08' => 'docs/wiki/design/30-skeleton/my-activities.md',
        'P-10' => 'docs/wiki/design/30-skeleton/chapters.md',
        'P-11' => 'docs/wiki/design/30-skeleton/chapters.md',
        'P-12' => 'docs/wiki/design/30-skeleton/getting-started.md',
        'P-13' => 'docs/wiki/design/30-skeleton/help-out.md',
        'P-14' => 'docs/wiki/design/30-skeleton/about.md',
        'P-15' => 'docs/wiki/design/30-skeleton/about.md',
        'P-16' => 'docs/wiki/design/30-skeleton/about.md',
        'P-17' => 'docs/wiki/design/30-skeleton/about.md',
        'P-18' => 'docs/wiki/design/30-skeleton/about.md',
        'P-19' => 'docs/wiki/design/30-skeleton/about.md',
        'P-20' => 'docs/wiki/design/30-skeleton/about.md',
    ],

    // Drift heuristics for "is the view still a stub?"
    'stub_line_threshold' => 12,
    'stub_markers' => [
        '[placeholder',
        'placeholder-pattern',
        'Lorem ipsum',
        'TODO',
    ],

    // Registry slug → built artefacts, for code-drift checks.
    //   view  : the Blade file that renders the page (null = unknown/none → skip stub check).
    //   route : the locale-agnostic route URI to expect (the {locale} prefix is
    //           stripped in DriftChecker; matches `php artisan route:list`).
    // The model views (Activity→events, Group→chapters, Article→news) are built;
    // the static about/*, getting-started, membership, contact, legal views are
    // routed but not yet created — their rows stay Wire 🔴 so no false drift.
    'pages' => [
        '/' => ['view' => 'resources/views/home.blade.php', 'route' => '/'],
        '/events' => ['view' => 'resources/views/activities/index.blade.php', 'route' => 'events'],
        '/events/[slug]' => ['view' => 'resources/views/activities/show.blade.php', 'route' => 'events/{activity}'],
        '/membership' => ['view' => null, 'route' => 'membership'],
        '/contact' => ['view' => null, 'route' => 'contact'],
        '/privacy' => ['view' => null, 'route' => 'privacy'],
        '/login' => ['view' => null, 'route' => 'login'],
        '/chapters' => ['view' => 'resources/views/groups/index.blade.php', 'route' => 'chapters'],
        '/chapters/[postal-code]' => ['view' => 'resources/views/groups/show.blade.php', 'route' => 'chapters/{group}'],
        '/getting-started' => ['view' => null, 'route' => 'getting-started'],
        '/help-out' => ['view' => null, 'route' => 'help-out'],
        '/about' => ['view' => null, 'route' => 'about'],
        '/about/mission' => ['view' => null, 'route' => 'about/mission'],
        '/about/vision' => ['view' => null, 'route' => 'about/vision'],
        '/about/organisation' => ['view' => null, 'route' => 'about/organisation'],
        '/about/news' => ['view' => 'resources/views/articles/index.blade.php', 'route' => 'about/news'],
        '/about/press' => ['view' => null, 'route' => 'about/press'],
        '/about/partners' => ['view' => null, 'route' => 'about/partners'],
        '/admin' => ['view' => null, 'route' => 'admin'],
    ],
];
