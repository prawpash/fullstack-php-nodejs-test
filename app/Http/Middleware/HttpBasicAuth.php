<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuth extends AbstractBasicAuth
{
    /**
     * Build the unauthorized response for the documentation.
     */
    protected function unauthorized(): Response
    {
        return response('Unauthorized.', 401, [
            'WWW-Authenticate' => 'Basic realm="Restricted API Documentation"',
        ]);
    }
}
