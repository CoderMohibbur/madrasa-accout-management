<?php

namespace App\Services\Auth;

use App\Contracts\Auth\GoogleOAuthBroker;
use App\Data\Auth\GoogleOAuthUser;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class SocialiteGoogleOAuthBroker implements GoogleOAuthBroker
{
    public function redirect(): RedirectResponse
    {
        return $this->driver()->redirect();
    }

    public function user(): GoogleOAuthUser
    {
        $socialiteUser = $this->driver()->user();

        return new GoogleOAuthUser(
            subject: (string) $socialiteUser->getId(),
            email: $this->normalizeEmail($socialiteUser->getEmail()),
            emailVerified: $this->resolveVerifiedFlag($socialiteUser),
            name: $socialiteUser->getName(),
            avatar: $socialiteUser->getAvatar(),
        );
    }

    private function driver(): mixed
    {
        $this->ensureConfigured();

        if (! class_exists(\Laravel\Socialite\SocialiteManager::class)) {
            throw new RuntimeException('Google sign-in dependency is not installed yet. Run composer install for the prompt-38 Socialite scaffolding before using this flow.');
        }

        return (new \Laravel\Socialite\SocialiteManager(app()))
            ->driver('google')
            ->scopes(['openid', 'profile', 'email']);
    }

    private function ensureConfigured(): void
    {
        $clientId = trim((string) config('services.google.client_id'));
        $clientSecret = trim((string) config('services.google.client_secret'));
        $redirect = trim((string) config('services.google.redirect'));

        $usesPlaceholders = $clientId === 'replace-me-google-client-id'
            || $clientSecret === 'replace-me-google-client-secret'
            || $redirect === 'https://example.test/auth/google/callback';

        if ($clientId === '' || $clientSecret === '' || $redirect === '' || $usesPlaceholders) {
            throw new RuntimeException('Google sign-in is scaffolded, but this environment is still using placeholder credentials. Replace the prompt-08 OAuth placeholders before live use.');
        }
    }

    private function normalizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        $normalized = trim(strtolower($email));

        return $normalized === '' ? null : $normalized;
    }

    private function resolveVerifiedFlag(mixed $socialiteUser): bool
    {
        return filter_var(
            data_get($socialiteUser->user, 'email_verified'),
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE,
        ) ?? false;
    }
}
