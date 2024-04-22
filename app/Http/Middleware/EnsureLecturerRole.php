<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLecturerRole
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is an admin
        if (session()->get('user_role') !== 'lecturer') {
            // Redirect if not an admin
            return redirect('/login')->withErrors(['login_error' => 'Please login']);
        }

        return $next($request);
    }
}
