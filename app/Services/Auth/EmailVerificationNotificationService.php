<?php

namespace App\Services\Auth;

use App\Models\User;
use Throwable;

class EmailVerificationNotificationService
{
    public function __construct(
        private readonly ContactVerificationAuditLogger $auditLogger,
    ) {
    }

    public function send(User $user, string $source): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            $this->auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.email.send_requested',
                summary: 'Email verification notification issued for the account.',
                context: [
                    'source' => $source,
                    'email' => $user->email,
                ],
            );

            return true;
        } catch (Throwable $exception) {
            report($exception);

            $this->auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.email.delivery_deferred',
                summary: 'Email verification could not be delivered in the current environment.',
                context: [
                    'source' => $source,
                    'email' => $user->email,
                    'error' => $exception->getMessage(),
                ],
            );

            return false;
        }
    }
}
