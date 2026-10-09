<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Upload Rules
    |--------------------------------------------------------------------------
    |
    | Capped to match the application attachment limit (5 MB per file) so an
    | oversized file is rejected the moment it's chosen, not just on submit.
    |
    */

    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:5120'],
    ],

];
