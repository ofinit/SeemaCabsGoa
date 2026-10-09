<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/razorpay/webhook',
        'api/cashfree/webhook',
        // Website ad beacons from the static marketing pages (no session / token there).
        'ads/i',
        'ads/report',
    ];
}
