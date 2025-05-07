<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    // You can customize this class as needed

    protected $except = [
        'XSRF-TOKEN',
    ];
}
