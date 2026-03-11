<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'donor.access' => \App\Http\Middleware\EnsureDonorPortalAccess::class,
            'guardian.info.access' => \App\Http\Middleware\EnsureGuardianInformationalAccess::class,
            'management.surface' => \App\Http\Middleware\EnsureManagementSurfaceAccess::class,
            'guardian.protected' => \App\Http\Middleware\EnsureGuardianProtectedAccess::class,
            'portal.home' => \App\Http\Middleware\RedirectPortalUsersFromLegacyDashboard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
