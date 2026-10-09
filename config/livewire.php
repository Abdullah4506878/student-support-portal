<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Upload Rules
    |--------------------------------------------------------------------------
    |
    | Deliberately looser than the 5 MB business limit (enforced with a
    | friendly, filename-specific message in app/Concerns/ApplicationValidationRules
    | on submit) and kept below the server's upload_max_filesize (10 MB), so a
    | file under this ceiling always arrives whole and gets our own message
    | instead of a generic one from this stage — or, if this stage's cap is
    | hit, silently truncated-looking failures are never misreported as a
    | wrong file type.
    |
    */

    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:9216'],
    ],

];
