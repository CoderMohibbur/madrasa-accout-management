<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\ContactVerificationAuditLogger;
use App\Services\MultiRole\MultiRoleContextResolver;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(
        EmailVerificationRequest $request,
        ContactVerificationAuditLogger $auditLogger,
        MultiRoleContextResolver $multiRoleContextResolver,
    ): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->to($this->verifiedRedirectPath(
                $user,
                $multiRoleContextResolver,
            ));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            $auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.email.verified',
                summary: 'Email verification completed successfully.',
                before: [
                    'email_verified_at' => null,
                ],
                after: [
                    'email_verified_at' => $user->fresh()->email_verified_at?->toIso8601String(),
                ],
            );
        }

        $routeName = $multiRoleContextResolver->defaultVerifiedRouteName($user);
        $redirectPath = route($routeName, absolute: false);

        if ($routeName === 'dashboard') {
            $redirectPath .= '?verified=1';
        }

        return redirect()->to($redirectPath)
            ->with('email_verification_message', 'Your email address has been verified.');
    }

    private function verifiedRedirectPath(
        \App\Models\User $user,
        MultiRoleContextResolver $multiRoleContextResolver,
    ): string
    {
        $routeName = $multiRoleContextResolver->defaultVerifiedRouteName($user);
        $redirectPath = route($routeName, absolute: false);

        if ($routeName === 'dashboard') {
            $redirectPath .= '?verified=1';
        }

        return $redirectPath;
    }
}
