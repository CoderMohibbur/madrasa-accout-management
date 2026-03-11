<?php

namespace App\Services\GuardianPortal;

use App\Models\Guardian;

class GuardianInformationalAccessState
{
    public function __construct(
        public readonly bool $hasAccessibleAccountState,
        public readonly bool $hasGuardianContext,
        public readonly bool $protectedEligible,
        public readonly ?Guardian $guardian,
        public readonly string $reason,
    ) {
    }
}
