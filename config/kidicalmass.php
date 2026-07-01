<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email setting
    |--------------------------------------------------------------------------
    */

    'mail' => [
        // Email address where contact form submissions should be sent.
        'communications' => env('MAIL_COMMUNICATIONS_ADDRESS', env('MAIL_FROM_ADDRESS')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default images
    |--------------------------------------------------------------------------
    | Fallback images used when a model has no main image uploaded.
    | Each key maps a model base class name to its default image slug.
    | The URL resolves to: img/defaults/{slug}.jpg (+ -card, -thumb for conversions).
    */

    'default_images' => [
        'Group' => 'group',
        'Activity' => 'activity',
        'Article' => 'article',
    ],
];
