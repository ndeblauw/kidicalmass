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
];
