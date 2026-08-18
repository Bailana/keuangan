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

    /*
    |--------------------------------------------------------------------------
    | Parent Support Fee
    |--------------------------------------------------------------------------
    |
    | This value represents the fixed monthly parent support fee charged to
    | children who have the parent support option enabled.
    |
    */

    'parent_support_fee' => env('PARENT_SUPPORT_FEE', 25000),
];
