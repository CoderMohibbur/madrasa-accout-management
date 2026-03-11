<?php

namespace App\Http\Middleware;

use App\Services\MultiRole\MultiRoleContextResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPortalUsersFromLegacyDashboard
{
    public function __construct(
        private readonly MultiRoleContextResolver $multiRoleContextResolver,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAccessibleAccountState()) {
            return $next($request);
        }

        $singleContextRouteName = $this->multiRoleContextResolver->singleEligibleContextRouteName($user);

        if ($singleContextRouteName !== null) {
            return redirect()->route($singleContextRouteName);
        }

        return $next($request);
    }
}
