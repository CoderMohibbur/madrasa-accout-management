<?php

namespace App\Http\Middleware;

use App\Services\GuardianPortal\GuardianInformationalPortalData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuardianInformationalAccess
{
    public function __construct(
        private readonly GuardianInformationalPortalData $guardianInformationalPortalData,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->guardianInformationalPortalData->requireInformationalAccess($user);

        return $next($request);
    }
}
