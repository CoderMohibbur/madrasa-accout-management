<?php

namespace App\Services\Auth;

use App\Data\Auth\GoogleOAuthUser;
use App\Models\Donor;
use App\Models\ExternalIdentity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleSignInService
{
    public function signInWithGoogle(GoogleOAuthUser $providerUser, string $intent = 'public'): GoogleSignInOutcome
    {
        $identity = ExternalIdentity::query()
            ->where('provider', ExternalIdentity::PROVIDER_GOOGLE)
            ->where('provider_subject', $providerUser->subject)
            ->first();

        if ($identity) {
            $user = $identity->user()->firstOrFail();

            $this->touchIdentity($identity, $providerUser);
            $this->applyVerifiedEmailToMatchingAccount($user, $providerUser);

            if (! $user->hasAccessibleAccountState()) {
                return $this->blockedOutcome($user);
            }

            return new GoogleSignInOutcome(
                user: $user,
                status: 'existing_identity',
                message: 'Google sign-in is ready on your shared account.',
                shouldLogin: true,
            );
        }

        if (! $providerUser->emailVerified || blank($providerUser->email)) {
            return new GoogleSignInOutcome(
                user: null,
                status: 'missing_verified_email',
                message: 'Google sign-in needs a verified email address before it can create or auto-link a shared account.',
            );
        }

        $intent = $this->normalizeIntent($intent);
        $user = User::query()->where('email', $providerUser->email)->first();

        if ($user) {
            $existingGoogleIdentity = $user->googleIdentity()->first();

            if ($existingGoogleIdentity && $existingGoogleIdentity->provider_subject !== $providerUser->subject) {
                return new GoogleSignInOutcome(
                    user: $user,
                    status: 'provider_conflict',
                    message: 'This shared account is already linked to a different Google identity. Leave the existing link in place and use recovery or support if the account needs to change.',
                );
            }

            $identity = $existingGoogleIdentity ?? $this->createIdentity($user, $providerUser);
            $this->touchIdentity($identity, $providerUser);
            $this->applyVerifiedEmailToMatchingAccount($user, $providerUser);

            if (! $user->hasAccessibleAccountState()) {
                return $this->blockedOutcome($user);
            }

            return new GoogleSignInOutcome(
                user: $user,
                status: 'linked_existing_user',
                message: 'Google sign-in has been linked to your existing shared account.',
                shouldLogin: true,
            );
        }

        if ($intent === 'guardian') {
            return new GoogleSignInOutcome(
                user: null,
                status: 'guardian_first_time_deferred',
                message: 'First-time guardian Google onboarding stays deferred in this rollout. Create the guardian foundation with email first, then link Google from your account.',
            );
        }

        $user = $this->createUserFromGoogle($providerUser);

        if ($intent === 'donor') {
            $this->createDonorFoundation($user);
        }

        $this->createIdentity($user, $providerUser);

        return new GoogleSignInOutcome(
            user: $user,
            status: $intent === 'donor' ? 'created_donor_user' : 'created_public_user',
            message: $intent === 'donor'
                ? 'Your shared account is ready, donor intent has been recorded, and Google sign-in can now reuse this same identity.'
                : 'Your shared account is ready, and Google sign-in can now reuse this same identity.',
            shouldLogin: true,
        );
    }

    public function linkAuthenticatedUser(User $user, GoogleOAuthUser $providerUser): GoogleSignInOutcome
    {
        $existingIdentity = ExternalIdentity::query()
            ->where('provider', ExternalIdentity::PROVIDER_GOOGLE)
            ->where('provider_subject', $providerUser->subject)
            ->first();

        if ($existingIdentity && $existingIdentity->user_id !== $user->getKey()) {
            return new GoogleSignInOutcome(
                user: $user,
                status: 'provider_conflict',
                message: 'This Google identity is already linked to a different shared account. It was left unchanged.',
            );
        }

        $currentGoogleIdentity = $user->googleIdentity()->first();

        if ($currentGoogleIdentity && $currentGoogleIdentity->provider_subject !== $providerUser->subject) {
            return new GoogleSignInOutcome(
                user: $user,
                status: 'provider_conflict',
                message: 'This shared account is already linked to a different Google identity. Prompt-38 keeps broad Google merge or reassignment logic disabled.',
            );
        }

        $identity = $currentGoogleIdentity ?? $existingIdentity ?? $this->createIdentity($user, $providerUser);

        $this->touchIdentity($identity, $providerUser);
        $this->applyVerifiedEmailToMatchingAccount($user, $providerUser);

        return new GoogleSignInOutcome(
            user: $user,
            status: 'linked_authenticated_user',
            message: 'Google sign-in is now linked to this shared account.',
        );
    }

    private function normalizeIntent(string $intent): string
    {
        return in_array($intent, ['public', 'donor', 'guardian'], true)
            ? $intent
            : 'public';
    }

    private function blockedOutcome(User $user): GoogleSignInOutcome
    {
        if (! $user->hasApprovedAccountAccess()) {
            return new GoogleSignInOutcome(
                user: $user,
                status: 'approval_pending',
                message: 'Google sign-in linked correctly, but this shared account is still waiting on approval.',
            );
        }

        return new GoogleSignInOutcome(
            user: $user,
            status: 'account_blocked',
            message: 'Google sign-in linked correctly, but this shared account is inactive or blocked right now.',
        );
    }

    private function createUserFromGoogle(GoogleOAuthUser $providerUser): User
    {
        $user = User::query()->create([
            'name' => $providerUser->name ?: 'Google User',
            'email' => $providerUser->email,
            'password' => Hash::make(Str::random(40)),
            'email_verified_at' => $providerUser->emailVerified ? now() : null,
            'approval_status' => User::APPROVAL_NOT_REQUIRED,
            'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        ]);

        $this->ensureRegisteredUserRoleExists();
        $user->assignRole(User::ROLE_REGISTERED_USER);

        return $user;
    }

    private function createDonorFoundation(User $user): void
    {
        Donor::query()->firstOrCreate(
            ['user_id' => $user->getKey()],
            [
                'name' => $user->name,
                'email' => $user->email,
                'portal_enabled' => false,
                'address' => null,
                'notes' => 'Google sign-in donor intent.',
                'isActived' => false,
                'isDeleted' => false,
            ],
        );
    }

    private function ensureRegisteredUserRoleExists(): void
    {
        Role::query()->firstOrCreate(
            ['name' => User::ROLE_REGISTERED_USER],
            [
                'display_name' => 'Registered User',
                'description' => 'Self-registered account without portal eligibility.',
                'is_system' => true,
            ],
        );
    }

    private function createIdentity(User $user, GoogleOAuthUser $providerUser): ExternalIdentity
    {
        return $user->externalIdentities()->create([
            'provider' => ExternalIdentity::PROVIDER_GOOGLE,
            'provider_subject' => $providerUser->subject,
            'provider_email' => $providerUser->email,
            'provider_email_verified' => $providerUser->emailVerified,
            'linked_at' => now(),
            'last_used_at' => now(),
        ]);
    }

    private function touchIdentity(ExternalIdentity $identity, GoogleOAuthUser $providerUser): void
    {
        $identity->forceFill([
            'provider_email' => $providerUser->email,
            'provider_email_verified' => $providerUser->emailVerified,
            'last_used_at' => now(),
        ])->save();
    }

    private function applyVerifiedEmailToMatchingAccount(User $user, GoogleOAuthUser $providerUser): void
    {
        if (! $providerUser->emailVerified || blank($providerUser->email) || $user->hasVerifiedEmail()) {
            return;
        }

        if (Str::lower((string) $user->email) !== $providerUser->email) {
            return;
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();
    }
}
