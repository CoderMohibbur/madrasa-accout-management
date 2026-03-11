<?php

namespace App\Services\DonorPortal;

use App\Models\Donor;

class DonorAccessState
{
    public function __construct(
        public readonly bool $hasAccessibleAccountState,
        public readonly bool $hasDonorContext,
        public readonly bool $portalEligible,
        public readonly ?Donor $donor,
        public readonly string $reason,
    ) {
    }

    public function requiresNoPortalView(): bool
    {
        return $this->hasDonorContext && ! $this->portalEligible;
    }
}
