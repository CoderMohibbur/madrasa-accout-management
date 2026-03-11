<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagementSurfaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if (! $user->hasAccessibleAccountState()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->hasRole('management')) {
            return $next($request);
        }

        if ($user->hasRole(\App\Models\User::ROLE_REGISTERED_USER)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->guardianProfile()->exists()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->donorProfile()->exists()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if (! $user->hasAnyRole(['guardian', 'donor'])) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN);
    }
}
