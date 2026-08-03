<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Documentation Access
    |--------------------------------------------------------------------------
    |
    | HTTP Basic Auth credentials used to protect the API documentation
    | (the Scalar UI and the generated OpenAPI document).
    |
    */

    'auth' => [
        'username' => env('DOCS_BASIC_AUTH_USERNAME', ''),
        'password' => env('DOCS_BASIC_AUTH_PASSWORD', ''),
    ],

];
