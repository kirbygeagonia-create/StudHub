<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School identity
    |--------------------------------------------------------------------------
    |
    | StudHub is single-tenant in v1. These values seed the `schools` row
    | for the host institution (SEAIT for the pilot).
    |
    */

    'school_name' => env('STUDHUB_SCHOOL_NAME', 'South East Asian Institute of Technology, Inc.'),
    'school_short' => env('STUDHUB_SCHOOL_SHORT', 'SEAIT'),

    /*
    |--------------------------------------------------------------------------
    | Allowed email domains
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of email domains permitted to register. Empty
    | string means "any domain" (only useful in local dev / CI).
    |
    */

    'allowed_email_domains' => env('STUDHUB_ALLOWED_EMAIL_DOMAINS', 'seait.edu.ph,students.seait.edu.ph'),

    /*
    |--------------------------------------------------------------------------
    | Year level bounds
    |--------------------------------------------------------------------------
    |
    | Inclusive min and max year level a student may pick during onboarding.
    | BSCE goes to year 5; everything else is 1–4. We allow 1–5 across the
    | board and let the UI gate against `programs.default_year_levels`.
    |
    */

    'year_level_min' => 1,
    'year_level_max' => 5,

];
