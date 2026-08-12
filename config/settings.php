<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Fee (SPP)
    |--------------------------------------------------------------------------
    |
    | This value represents the fixed monthly school fee (SPP) charged to
    | children enrolled in the school service.
    |
    */

    'school_fee' => env('SCHOOL_FEE', 1000000),

    /*
    |--------------------------------------------------------------------------
    | Default Sessions per Month
    |--------------------------------------------------------------------------
    |
    | Default number of therapy/vokasi sessions per month when not specified.
    |
    */

    'default_sessions' => 4,
];
