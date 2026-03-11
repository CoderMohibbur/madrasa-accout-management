<?php

namespace App\Services\GuardianPortal;

use App\Models\User;
use App\Services\DonorPortal\DonorPortalData;
use App\Services\Portal\ExternalAdmissionUrlResolver;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GuardianInformationalPortalData
{
    public function __construct(
        private readonly DonorPortalData $donorPortalData,
        private readonly GuardianPortalData $guardianPortalData,
        private readonly ExternalAdmissionUrlResolver $externalAdmissionUrlResolver,
    ) {
    }

    public function resolveAccess(User $user): GuardianInformationalAccessState
    {
        $guardian = $user->guardianProfile()->first();
        $protectedAccess = $this->guardianPortalData->resolveAccess($user);
        $hasGuardianContext = ! is_null($guardian) || $user->hasRole('guardian');

        $reason = match (true) {
            ! $user->hasAccessibleAccountState() => 'account_blocked',
            $protectedAccess->reason === 'email_unverified' => 'email_unverified',
            $protectedAccess->reason === 'unlinked' => 'unlinked',
            $guardian && $guardian->isDeleted => 'profile_deleted',
            $guardian && ! $guardian->isActived => 'profile_inactive',
            $protectedAccess->protectedEligible => 'protected_eligible',
            $guardian && ! $guardian->portal_enabled => 'profile_pending',
            ! is_null($guardian) => 'informational_only',
            $user->hasRole('guardian') => 'role_only',
            default => 'none',
        };

        return new GuardianInformationalAccessState(
            hasAccessibleAccountState: $user->hasAccessibleAccountState(),
            hasGuardianContext: $hasGuardianContext,
            protectedEligible: $protectedAccess->protectedEligible,
            guardian: $guardian,
            reason: $reason,
        );
    }

    public function requireInformationalAccess(User $user): GuardianInformationalAccessState
    {
        $access = $this->resolveAccess($user);

        if (! $access->hasAccessibleAccountState || ! $access->hasGuardianContext) {
            throw new HttpException(403, 'This account is not allowed to access guardian informational routes.');
        }

        return $access;
    }

    public function shouldUseInformationalHome(User $user): bool
    {
        if ($user->hasRole('management') || $this->donorPortalData->resolveAccess($user)->hasDonorContext) {
            return false;
        }

        $access = $this->resolveAccess($user);

        if (! $access->hasAccessibleAccountState || ! $access->hasGuardianContext) {
            return false;
        }

        return ! $access->protectedEligible;
    }

    public function admissionExternalUrl(): ?string
    {
        return $this->externalAdmissionUrlResolver->resolve();
    }
}
