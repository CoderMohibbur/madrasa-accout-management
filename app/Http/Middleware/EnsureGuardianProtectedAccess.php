<?php

namespace App\Http\Middleware;

use App\Services\GuardianPortal\GuardianPortalData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuardianProtectedAccess
{
    public function __construct(
        private readonly GuardianPortalData $guardianPortalData,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->guardianPortalData->requireProtectedAccess($user);

        return $next($request);
    }
}
