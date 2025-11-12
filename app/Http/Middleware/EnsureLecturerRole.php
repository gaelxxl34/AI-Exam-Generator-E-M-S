<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLecturerRole
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user session exists
        if (!session()->has('user') || !session()->has('user_role')) {
            // Clear session and redirect with session expired message
            session()->flush();
            return redirect('/login')->with('session_expired', 'Your session has expired. Please login again to continue.');
        }

        // Check if the user is a lecturer
        if (session()->get('user_role') !== 'lecturer') {
            // Redirect if not a lecturer
            return redirect('/login')->withErrors(['login_error' => 'Access denied. You need lecturer privileges to access this page.']);
        }

        return $next($request);
    }
}
