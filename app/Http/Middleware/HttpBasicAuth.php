<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = User::admins()->first();

        if ($admin === null) {
            abort(503, 'No admin user found. Run the database seeder first.');
        }

        $username = $request->getUser();
        $password = $request->getPassword();

        if ($username !== $admin->email || ! Hash::check($password, $admin->password)) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Restricted API Documentation"',
            ]);
        }

        return $next($request);
    }
}
