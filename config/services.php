<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'mailerlite' => [
        'base_url' => env('MAILERLITE_API_URL', 'https://connect.mailerlite.com/api'),
        'token' => env('MAILERLITE_TOKEN'),
        'groups' => [
            'nl' => env('MAILERLITE_NEWSLETTER_NL_GROUP_ID'),
            'fr' => env('MAILERLITE_NEWSLETTER_FR_GROUP_ID'),
        ],
        // Web preview of the most recent edition per language, shown beside the
        // signup form so visitors can see what they sign up for.
        'latest_edition' => [
            'nl' => env('MAILERLITE_LATEST_EDITION_NL', 'https://preview.mailerlite.io/preview/2451619/emails/198242852396861310'),
            'fr' => env('MAILERLITE_LATEST_EDITION_FR', 'https://preview.mailerlite.io/preview/2451619/emails/196949199985575207'),
        ],
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
