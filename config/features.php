<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Remote SaaS modules (courses/membership, signals, copy-trade)
    |--------------------------------------------------------------------------
    |
    | When false (default), membership / signal / subscription module flags
    | are forced off in shared view data so Planned SaaS features stay
    | disabled without deleting code. See docs/database/FEATURE_CLASSIFICATION.md.
    |
    */
    'saas_remote_modules' => env('FEATURE_SAAS_REMOTE_MODULES', false),

];
