<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SessionService;

class TrackSessionActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only track activity for authenticated users
        if (session()->has('user')) {
            // Update session activity every 5 minutes to reduce database writes
            $lastActivity = session('last_activity_update');
            $now = time();
            
            if (!$lastActivity || ($now - $lastActivity) > 300) { // 5 minutes
                app(SessionService::class)->updateActivity();
                session(['last_activity_update' => $now]);
            }
        }

        return $next($request);
    }
}
