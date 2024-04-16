<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is an admin
        if (session()->get('user_role') !== 'admin') {
            // Redirect if not an admin
            return redirect('/login')->withErrors(['login_error' => 'Access denied please login']);
        }

        return $next($request);
    }
}
