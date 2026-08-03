<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiBasicAuth extends AbstractBasicAuth
{
    /**
     * Build the unauthorized response for the API.
     */
    protected function unauthorized(): Response
    {
        return response()->json(['message' => 'Unauthorized.'], 401, [
            'WWW-Authenticate' => 'Basic realm="API"',
        ]);
    }
}
