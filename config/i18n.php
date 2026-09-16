<?php

return [
    'show_missing_translation_keys' => env('I18N_SHOW_MISSING_TRANSLATION_KEYS', false),
    'show_review_markers' => env('I18N_SHOW_REVIEW_MARKERS', env('APP_ENV') === 'staging'),
];
