<?php

namespace App\Http\Middleware;

use App\Services\DonorPortal\DonorPortalData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDonorPortalAccess
{
    public function __construct(
        private readonly DonorPortalData $donorPortalData,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->donorPortalData->requireDonorAccess($user);

        return $next($request);
    }
}
