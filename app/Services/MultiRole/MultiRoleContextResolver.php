<?php

namespace App\Services\MultiRole;

use App\Models\User;
use App\Services\DonorPortal\DonorPortalData;
use App\Services\GuardianPortal\GuardianInformationalPortalData;
use App\Services\GuardianPortal\GuardianPortalData;
use Illuminate\Support\Str;

class MultiRoleContextResolver
{
    public function __construct(
        private readonly DonorPortalData $donorPortalData,
        private readonly GuardianPortalData $guardianPortalData,
        private readonly GuardianInformationalPortalData $guardianInformationalPortalData,
    ) {
    }

    /**
     * @return array<int, array{
     *     key: string,
     *     title: string,
     *     route_name: string,
     *     route_patterns: array<int, string>,
     *     status: string,
     *     description: string,
     *     scope_note: string,
     *     badge_variant: string
     * }>
     */
    public function eligibleContexts(User $user): array
    {
        if (! $user->hasAccessibleAccountState()) {
            return [];
        }

        $contexts = [];

        $donorAccess = $this->donorPortalData->resolveAccess($user);

        if ($donorAccess->hasDonorContext) {
            $contexts[] = [
                'key' => 'donor',
                'title' => $donorAccess->portalEligible ? 'Donor Portal' : 'Donor Home',
                'route_name' => 'donor.dashboard',
                'route_patterns' => ['donor.*'],
                'status' => $donorAccess->portalEligible ? 'Portal eligible' : 'Donor context ready',
                'description' => match ($donorAccess->reason) {
                    'identified_only' => 'Use the donor surface for identified donation activity while donor portal history stays separately gated.',
                    'profile_pending' => 'The donor profile is linked, but full donor portal history is still pending on this account.',
                    'profile_inactive', 'profile_deleted' => 'A donor profile exists, but donor access remains limited to the donor-only surface until that state is corrected.',
                    'role_only' => 'Donor role membership exists on this account, but donor data still stays isolated on the donor surface only.',
                    default => 'Open the donor-only surface for donation status, history, and receipts that belong to this account only.',
                },
                'scope_note' => $donorAccess->portalEligible
                    ? 'Shows only donor-owned donation and receipt records.'
                    : 'Keeps donor context separate without exposing guardian-owned data.',
                'badge_variant' => 'warning',
            ];
        }

        $guardianProtectedAccess = $this->guardianPortalData->resolveAccess($user);

        if ($guardianProtectedAccess->protectedEligible) {
            $contexts[] = [
                'key' => 'guardian_protected',
                'title' => 'Guardian Portal',
                'route_name' => 'guardian.dashboard',
                'route_patterns' => [
                    'guardian.dashboard',
                    'guardian.students.*',
                    'guardian.invoices.*',
                    'guardian.history.*',
                ],
                'status' => 'Protected access ready',
                'description' => 'Open the protected guardian portal for linked students, invoices, payments, and receipts that belong only to authorized guardian links.',
                'scope_note' => 'Protected guardian records stay linkage-scoped and separate from donor-owned data.',
                'badge_variant' => 'success',
            ];

            return $contexts;
        }

        $guardianInformationalAccess = $this->guardianInformationalPortalData->resolveAccess($user);

        if ($guardianInformationalAccess->hasGuardianContext) {
            $contexts[] = [
                'key' => 'guardian_information',
                'title' => 'Guardian Information',
                'route_name' => 'guardian.info.dashboard',
                'route_patterns' => ['guardian.info.*'],
                'status' => 'Informational access ready',
                'description' => match ($guardianInformationalAccess->reason) {
                    'email_unverified' => 'Guardian information is available now while protected guardian access still waits on verified email.',
                    'unlinked' => 'Guardian information is available now while protected guardian access still waits on an authorized student linkage.',
                    'profile_pending' => 'Guardian information is live while protected guardian enablement remains separate and fail-closed.',
                    'profile_inactive', 'profile_deleted' => 'Guardian information stays limited to safe guidance while the guardian profile state needs review.',
                    'role_only' => 'Guardian-domain potential exists on this account, but protected access stays blocked until profile and linkage rules are satisfied.',
                    default => 'Open the guardian informational surface for institution guidance and safe next steps without exposing protected student or invoice data.',
                },
                'scope_note' => 'This informational surface never shows linked students, invoices, receipts, or payment-entry controls.',
                'badge_variant' => 'info',
            ];
        }

        return $contexts;
    }

    public function hasMultipleEligibleContexts(User $user): bool
    {
        return count($this->eligibleContexts($user)) > 1;
    }

    public function singleEligibleContextRouteName(User $user): ?string
    {
        $contexts = $this->eligibleContexts($user);

        return count($contexts) === 1
            ? $contexts[0]['route_name']
            : null;
    }

    public function defaultAuthRouteName(User $user): string
    {
        return $this->singleEligibleContextRouteName($user) ?? 'dashboard';
    }

    public function defaultVerifiedRouteName(User $user): string
    {
        $singleContextRoute = $this->singleEligibleContextRouteName($user);

        if ($singleContextRoute !== null) {
            return $singleContextRoute;
        }

        if ($this->hasMultipleEligibleContexts($user)) {
            return 'dashboard';
        }

        return $user->hasRole(User::ROLE_REGISTERED_USER)
            ? 'registration.onboarding'
            : 'dashboard';
    }

    /**
     * @return array<int, array{
     *     key: string,
     *     title: string,
     *     route_name: string,
     *     route_patterns: array<int, string>,
     *     status: string,
     *     description: string,
     *     scope_note: string,
     *     badge_variant: string
     * }>
     */
    public function switchableContexts(User $user, ?string $currentRouteName = null): array
    {
        $contexts = $this->eligibleContexts($user);

        if (count($contexts) < 2) {
            return [];
        }

        if ($currentRouteName === null || $currentRouteName === '') {
            return $contexts;
        }

        return array_values(array_filter(
            $contexts,
            fn (array $context): bool => ! $this->contextMatchesRouteName($context, $currentRouteName),
        ));
    }

    /**
     * @param  array{route_name: string, route_patterns: array<int, string>}  $context
     */
    private function contextMatchesRouteName(array $context, string $routeName): bool
    {
        if ($routeName === $context['route_name']) {
            return true;
        }

        foreach ($context['route_patterns'] as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }
}
