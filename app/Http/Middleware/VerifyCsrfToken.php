<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // Remove xendit entries since they're now in API routes
        'xendit/webhook',
        'api/xendit/webhook',
        'xendit/*',

        // Keep other webhook exclusions if you have them
        'webhook/*',
        'api/webhooks/*',

        'webhook/xendit',
    ];
}
