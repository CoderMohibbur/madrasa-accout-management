<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalUsersFromLegacyDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasRole('management')) {
            return $next($request);
        }

        if ($user->hasRole('guardian')) {
            return redirect()->route('guardian.dashboard');
        }

        if ($user->hasRole('donor')) {
            return redirect()->route('donor.dashboard');
        }

        return $next($request);
    }
}
