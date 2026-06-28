<?php

return [
    'fontawesomekit_url' => 'https://kit.fontawesome.com/0bde3bbac3.js',

    'vite' => false,
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
            'title' => 'Jaarcijfers',
            'color' => 'sky',
            'link' => 'admin/year-stats',
            'icon' => 'fa-chart-bar',
        ],
        [
            'title' => 'Contact Submissions',
            'color' => 'rose',
            'link' => 'admin/contact-forms',
            'icon' => 'fa-envelope',
        ],
        [
            'title' => 'Users',
            'color' => 'blue',
            'link' => 'admin/users',
            'icon' => 'fa-user',
        ],
        [
            'title' => 'Partners',
            'color' => 'violet',
            'link' => 'admin/partners',
            'icon' => 'fa-user-group',
        ],
        [
            'title' => 'Chapters',
            'color' => 'sky',
            'link' => 'admin/groups',
            'icon' => 'fa-flag',
        ],
    ],

    'details_for' => 'Details for',
    'record_of_type' => 'Record of the type',
    'create_new' => 'Create New',
];
