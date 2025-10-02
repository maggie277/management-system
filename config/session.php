<?php

use Illuminate\Support\Str;

return [

    'driver' =>env('SESSION_DRIVER', 'database'),
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION', null),
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', false),
    'http_only' => true,
    'same_site' => 'lax',
    'staff_cookie' => env('SESSION_STAFF_COOKIE', 'staff_session'),
    'staff_cookie_path' => '/staff',
    'staff_cookie_domain' => env('SESSION_STAFF_DOMAIN', null),
    'staff_cookie_secure' => env('SESSION_STAFF_SECURE_COOKIE', false),
    'staff_cookie_http_only' => true,
    'staff_cookie_same_site' => 'lax',

];
