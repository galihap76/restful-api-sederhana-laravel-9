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
        //
        'http://59a9-36-68-55-177.ngrok-free.app/posts',
        'http://59a9-36-68-55-177.ngrok-free.app/posts/1'
    ];
}
