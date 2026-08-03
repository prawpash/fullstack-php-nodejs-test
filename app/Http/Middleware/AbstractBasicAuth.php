<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractBasicAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        $user = $username
            ? User::where('email', $username)->where('active', true)->first()
            : null;

        if ($user === null || ! Hash::check($password, $user->password)) {
            return $this->unauthorized();
        }

        Auth::guard('web')->setUser($user);

        return $next($request);
    }

    /**
     * Build the unauthorized response for this middleware.
     */
    abstract protected function unauthorized(): Response;
}
