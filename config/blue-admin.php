<?php

return [
    'fontawesomekit_url' => 'https://kit.fontawesome.com/0bde3bbac3.js',

    'vite' => true,
    'livewire_v3' => false,
    'livewire_v4' => true,

    'ckeditor' => true,

    'flux' => false,
    'flux-version' => 'v2',
    'flux-layout' => false,

    'font' => [
        'include' => 'https://fonts.bunny.net/css?family=sofia-sans',
        'family' => 'Sofia Sans',
    ],

    'filepond_temporary_files_disk' => 'local',
    'filepond_temporary_files_path' => 'filepond',

    // Gates all blue-admin /admin routes (alias registered in bootstrap/app.php).
    'admin_middleware' => 'admin',

    'menu' => [
        [
            'title' => 'Dashboard',
            'link' => 'admin',
            'icon' => 'fa-home',
        ],
        [
            'header' => 'Activities & news',
        ],
        [
            'title' => 'Activities',
            'color' => 'rose',
            'link' => 'admin/activities',
            'icon' => 'fa-calendar',
        ],
        [
            'title' => 'News Articles',
            'color' => 'rose',
            'link' => 'admin/articles',
            'icon' => 'fa-newspaper',
        ],
        [
            'title' => 'Press Articles',
            'color' => 'blue',
            'link' => 'admin/pressarticles',
            'icon' => 'fa-tv-retro',
        ],

        [
            'header' => 'People & communication',
        ],
        [
            'title' => 'Contact Submissions',
            'color' => 'rose',
            'link' => 'admin/contactforms',
            'icon' => 'fa-envelope',
        ],
        [
            'title' => 'Users',
            'color' => 'blue',
            'link' => 'admin/users',
            'icon' => 'fa-user',
        ],
        [
            'header' => 'General',
        ],
        [
            'title' => 'Chapters',
            'color' => 'sky',
            'link' => 'admin/groups',
            'icon' => 'fa-flag',
        ],
        [
            'title' => 'Jaarcijfers',
            'color' => 'sky',
            'link' => 'admin/yearstats',
            'icon' => 'fa-chart-bar',
        ],
        [
            'title' => 'Partners',
            'color' => 'violet',
            'link' => 'admin/partners',
            'icon' => 'fa-handshake',
        ],
        [
            'title' => 'Teamleden',
            'color' => 'violet',
            'link' => 'admin/teammembers',
            'icon' => 'fa-people-group',
        ],
        [
            'title' => 'Citaten',
            'color' => 'violet',
            'link' => 'admin/quotes',
            'icon' => 'fa-quote-left',
        ],
    ],

    'details_for' => 'Details for',
    'record_of_type' => 'Record of the type',
    'create_new' => 'Create New',
];
