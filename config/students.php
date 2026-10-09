<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Registration number format
    |--------------------------------------------------------------------------
    |
    | Example: SU92-BSSEM-F22-171. Capture group 1 is the program code
    | (must match the selected program), group 2 is the batch code
    | (F22 = Fall 2022, S23 = Spring 2023).
    |
    */

    'registration_no_pattern' => env('STUDENT_REGISTRATION_NO_PATTERN', '/^SU\d{2}-(BSSE|BSDS|BSAI)[ME]-([FS]\d{2})-\d{1,4}$/'),

    /*
    |--------------------------------------------------------------------------
    | University email domain
    |--------------------------------------------------------------------------
    */

    'email_domain' => env('STUDENT_EMAIL_DOMAIN', '@superior.edu.pk'),

    /*
    |--------------------------------------------------------------------------
    | Email must match registration number
    |--------------------------------------------------------------------------
    |
    | When enabled, the university email must be the lowercased
    | registration number plus the email domain above.
    |
    */

    'enforce_email_matches_registration_no' => env('STUDENT_EMAIL_MATCHES_REGISTRATION_NO', true),

    /*
    |--------------------------------------------------------------------------
    | Programs
    |--------------------------------------------------------------------------
    |
    | Code => label. The code must appear in the registration number.
    |
    */

    'programs' => [
        'BSSE' => 'BS Software Engineering',
        'BSDS' => 'BS Data Science',
        'BSAI' => 'BS Artificial Intelligence',
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration number examples
    |--------------------------------------------------------------------------
    |
    | Shown as the field hint/placeholder, matching the selected program.
    |
    */

    'registration_no_examples' => [
        'BSSE' => 'SU92-BSSEM-F22-171',
        'BSDS' => 'SU92-BSDSM-F23-045',
        'BSAI' => 'SU92-BSAIM-F23-031',
    ],

];
