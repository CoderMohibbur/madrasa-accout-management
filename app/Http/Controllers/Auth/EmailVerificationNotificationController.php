<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\ContactVerificationAuditLogger;
use App\Services\Auth\EmailVerificationNotificationService;
use App\Services\MultiRole\MultiRoleContextResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class EmailVerificationNotificationController extends Controller
{
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const RESEND_HOURLY_LIMIT = 6;

    /**
     * Send a new email verification notification.
     */
    public function store(
        Request $request,
        ContactVerificationAuditLogger $auditLogger,
        EmailVerificationNotificationService $emailVerificationNotificationService,
    ): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->to($this->verifiedRedirectPath(
                $user,
                app(MultiRoleContextResolver::class),
            ));
        }

        $this->ensureCanSend($user, $auditLogger);

        $sent = $emailVerificationNotificationService->send($user, 'manual_resend');
        RateLimiter::hit($this->resendCooldownKey($user), self::RESEND_COOLDOWN_SECONDS);
        RateLimiter::hit($this->resendHourlyKey($user), 3600);

        return back()->with(
            $sent ? 'email_verification_message' : 'email_verification_warning',
            $sent
                ? 'A new verification link has been sent to your email address.'
                : 'Email verification could not be delivered automatically in this environment. Configure mail transport and try again.',
        );
    }

    private function ensureCanSend(User $user, ContactVerificationAuditLogger $auditLogger): void
    {
        if (RateLimiter::tooManyAttempts($this->resendCooldownKey($user), 1)) {
            $auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.email.send_cooldown',
                summary: 'Email verification resend is cooling down.',
                context: [
                    'email' => $user->email,
                ],
            );

            throw ValidationException::withMessages([
                'email_verification' => 'Please wait '.RateLimiter::availableIn($this->resendCooldownKey($user)).' seconds before requesting another email verification link.',
            ]);
        }

        if (RateLimiter::tooManyAttempts($this->resendHourlyKey($user), self::RESEND_HOURLY_LIMIT)) {
            $auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.email.send_limit_reached',
                summary: 'Email verification resend reached the hourly limit.',
                context: [
                    'email' => $user->email,
                ],
            );

            throw ValidationException::withMessages([
                'email_verification' => 'Email verification is temporarily unavailable because the hourly resend limit was reached. Please try again later.',
            ]);
        }
    }

    private function resendCooldownKey(User $user): string
    {
        return 'verification.email.send.cooldown:'.$user->getKey().'|'.$user->email;
    }

    private function resendHourlyKey(User $user): string
    {
        return 'verification.email.send.hour:'.$user->getKey().'|'.$user->email;
    }

    private function verifiedRedirectPath(User $user, MultiRoleContextResolver $multiRoleContextResolver): string
    {
        return route(
            $multiRoleContextResolver->defaultVerifiedRouteName($user),
            absolute: false,
        );
    }
}
