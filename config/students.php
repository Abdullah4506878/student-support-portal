<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Registration number format
    |--------------------------------------------------------------------------
    |
    | Example: SU92-BSSEM-F22-171. The third segment ([FS]\d{2}) is also
    | the student's batch code (F22 = Fall 2022, S23 = Spring 2023).
    |
    */

    'registration_no_pattern' => env('STUDENT_REGISTRATION_NO_PATTERN', '/^SU\d{2}-BSSE[ME]-([FS]\d{2})-\d{1,4}$/'),

    /*
    |--------------------------------------------------------------------------
    | University email domain
    |--------------------------------------------------------------------------
    */

    'email_domain' => env('STUDENT_EMAIL_DOMAIN', '@superior.edu.pk'),

    /*
    |--------------------------------------------------------------------------
    | Default program
    |--------------------------------------------------------------------------
    |
    | Fixed for Phase 1 — shown on registration but not editable.
    |
    */

    'default_program' => env('STUDENT_DEFAULT_PROGRAM', 'BS Software Engineering'),

];
