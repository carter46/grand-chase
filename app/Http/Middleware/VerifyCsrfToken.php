<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'get-started/',
        '/serviceworker.js',
        'https://onlinetrader.sharedwithexpose.com/get-started',
        'api/7th-tradehub/v1/health',
        'api/7th-tradehub/v1/subscription/sync',
    ];
}