<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user session exists
        if (!session()->has('user') || !session()->has('user_role')) {
            // Clear session and redirect with session expired message
            session()->flush();
            return redirect('/login')->with('session_expired', 'Your session has expired. Please login again to continue.');
        }

        // Check if the user is a superadmin
        if (session()->get('user_role') !== 'superadmin') {
            // Redirect if not a superadmin
            return redirect('/login')->withErrors(['login_error' => 'Access denied. You need super admin privileges to access this page.']);
        }

        return $next($request);
    }
}
