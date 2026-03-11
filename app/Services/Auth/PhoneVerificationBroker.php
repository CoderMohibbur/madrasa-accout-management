<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PhoneVerificationBroker
{
    private const CODE_TTL_MINUTES = 10;
    private const MAX_VERIFY_ATTEMPTS = 5;
    private const SEND_COOLDOWN_SECONDS = 60;
    private const SEND_HOURLY_LIMIT = 5;
    private const ABUSE_COOLDOWN_SECONDS = 900;

    public function __construct(
        private readonly ContactVerificationAuditLogger $auditLogger,
    ) {
    }

    public function send(User $user): ?string
    {
        $phone = $user->normalizedPhone();

        if ($phone === null) {
            throw ValidationException::withMessages([
                'phone' => 'Add an account phone number before requesting a verification code.',
            ]);
        }

        $this->ensureCanSend($user, $phone);

        $code = (string) random_int(100000, 999999);

        $payload = [
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'issued_at' => now()->toIso8601String(),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES)->toIso8601String(),
        ];

        if (app()->environment(['local', 'testing'])) {
            $payload['plain_code'] = $code;
        }

        Cache::put($this->cacheKey($user), $payload, now()->addMinutes(self::CODE_TTL_MINUTES));

        RateLimiter::hit($this->sendCooldownKey($phone), self::SEND_COOLDOWN_SECONDS);
        RateLimiter::hit($this->sendHourlyKey($phone), 3600);

        $this->auditLogger->record(
            actor: $user,
            target: $user,
            event: 'verification.phone.send_requested',
            summary: 'Phone verification code issued for the account.',
            context: [
                'phone' => PhoneNumber::mask($phone),
                'expires_in_minutes' => self::CODE_TTL_MINUTES,
                'delivery_mode' => app()->environment(['local', 'testing'])
                    ? 'development_placeholder'
                    : 'replace_before_production',
            ],
        );

        return $payload['plain_code'] ?? null;
    }

    public function verify(User $user, string $code): void
    {
        $phone = $user->normalizedPhone();

        if ($phone === null) {
            throw ValidationException::withMessages([
                'phone' => 'Add an account phone number before verifying it.',
            ]);
        }

        $this->ensureNotLockedOut($user, $phone);

        $payload = Cache::get($this->cacheKey($user));

        if (! is_array($payload) || ($payload['phone'] ?? null) !== $phone || $this->payloadExpired($payload)) {
            Cache::forget($this->cacheKey($user));

            throw ValidationException::withMessages([
                'phone_verification_code' => 'The verification code is invalid or has expired. Request a new code and try again.',
            ]);
        }

        if (! Hash::check($code, (string) ($payload['code_hash'] ?? ''))) {
            $attempts = (int) ($payload['attempts'] ?? 0) + 1;
            $payload['attempts'] = $attempts;
            Cache::put($this->cacheKey($user), $payload, $this->cacheExpiry($payload));

            $this->auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.phone.verify_failed',
                summary: 'Phone verification failed because the submitted code did not match.',
                context: [
                    'phone' => PhoneNumber::mask($phone),
                    'attempts_used' => $attempts,
                    'attempt_limit' => self::MAX_VERIFY_ATTEMPTS,
                ],
            );

            if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
                Cache::forget($this->cacheKey($user));
                $this->applyAbuseCooldown($user, $phone, 'Repeated invalid phone verification attempts triggered a temporary cooldown.');
            }

            throw ValidationException::withMessages([
                'phone_verification_code' => 'The verification code is invalid. Please try again.',
            ]);
        }

        $conflictingOwner = User::query()
            ->whereKeyNot($user->getKey())
            ->where('phone', $phone)
            ->whereNotNull('phone_verified_at')
            ->where('account_status', User::ACCOUNT_STATUS_ACTIVE)
            ->whereNull('deleted_at')
            ->first();

        if ($conflictingOwner) {
            Cache::forget($this->cacheKey($user));

            $this->auditLogger->record(
                actor: $user,
                target: $user,
                event: 'verification.phone.verify_conflict',
                summary: 'Phone verification was blocked because another active account already owns the verified number.',
                context: [
                    'phone' => PhoneNumber::mask($phone),
                    'conflicting_user_id' => $conflictingOwner->getKey(),
                ],
            );

            throw ValidationException::withMessages([
                'phone_verification' => 'Phone verification could not be completed for this account. Please contact support if this number should belong to you.',
            ]);
        }

        $before = [
            'phone' => PhoneNumber::mask($user->phone),
            'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
        ];

        $user->forceFill([
            'phone' => $phone,
            'phone_verified_at' => now(),
        ])->save();

        Cache::forget($this->cacheKey($user));
        RateLimiter::clear($this->penaltyKey($user, $phone));

        $this->auditLogger->record(
            actor: $user,
            target: $user,
            event: 'verification.phone.verified',
            summary: 'Phone verification completed successfully.',
            before: $before,
            after: [
                'phone' => PhoneNumber::mask($user->phone),
                'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
            ],
        );
    }

    public function invalidate(User $user): void
    {
        Cache::forget($this->cacheKey($user));
    }

    public function currentTestingCode(User $user): ?string
    {
        if (! app()->environment(['local', 'testing'])) {
            return null;
        }

        $payload = Cache::get($this->cacheKey($user));

        if (! is_array($payload)) {
            return null;
        }

        return $payload['plain_code'] ?? null;
    }

    private function ensureCanSend(User $user, string $phone): void
    {
        $this->ensureNotLockedOut($user, $phone);

        if (RateLimiter::tooManyAttempts($this->sendCooldownKey($phone), 1)) {
            $this->recordLockout($user, $phone, 'verification.phone.send_cooldown', 'Phone verification resend is cooling down.');

            throw ValidationException::withMessages([
                'phone_verification' => 'Please wait '.RateLimiter::availableIn($this->sendCooldownKey($phone)).' seconds before requesting another phone verification code.',
            ]);
        }

        if (RateLimiter::tooManyAttempts($this->sendHourlyKey($phone), self::SEND_HOURLY_LIMIT)) {
            $this->recordLockout($user, $phone, 'verification.phone.send_limit_reached', 'Phone verification resend reached the hourly limit.');

            throw ValidationException::withMessages([
                'phone_verification' => 'Phone verification is temporarily unavailable because the hourly resend limit was reached. Please try again later.',
            ]);
        }
    }

    private function ensureNotLockedOut(User $user, string $phone): void
    {
        if (! RateLimiter::tooManyAttempts($this->penaltyKey($user, $phone), 1)) {
            return;
        }

        $this->recordLockout($user, $phone, 'verification.phone.cooldown_active', 'Phone verification is temporarily locked after repeated abuse.');

        throw ValidationException::withMessages([
            'phone_verification' => 'Phone verification is temporarily locked. Please wait '.RateLimiter::availableIn($this->penaltyKey($user, $phone)).' seconds and try again.',
        ]);
    }

    private function applyAbuseCooldown(User $user, string $phone, string $summary): void
    {
        RateLimiter::hit($this->penaltyKey($user, $phone), self::ABUSE_COOLDOWN_SECONDS);

        $this->recordLockout($user, $phone, 'verification.phone.cooldown_applied', $summary);
    }

    private function recordLockout(User $user, string $phone, string $event, string $summary): void
    {
        $this->auditLogger->record(
            actor: $user,
            target: $user,
            event: $event,
            summary: $summary,
            context: [
                'phone' => PhoneNumber::mask($phone),
            ],
        );
    }

    private function payloadExpired(array $payload): bool
    {
        $expiresAt = Carbon::parse((string) ($payload['expires_at'] ?? now()->toIso8601String()));

        return $expiresAt->isPast();
    }

    private function cacheExpiry(array $payload): Carbon
    {
        return Carbon::parse((string) ($payload['expires_at'] ?? now()->addMinutes(self::CODE_TTL_MINUTES)->toIso8601String()));
    }

    private function cacheKey(User $user): string
    {
        return 'verification.phone.code.user:'.$user->getKey();
    }

    private function sendCooldownKey(string $phone): string
    {
        return 'verification.phone.send.cooldown:'.$phone.'|'.request()->ip();
    }

    private function sendHourlyKey(string $phone): string
    {
        return 'verification.phone.send.hour:'.$phone.'|'.request()->ip();
    }

    private function penaltyKey(User $user, string $phone): string
    {
        return 'verification.phone.penalty.user:'.$user->getKey().':'.$phone;
    }
}
