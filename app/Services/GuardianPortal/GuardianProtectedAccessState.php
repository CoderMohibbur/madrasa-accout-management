<?php

namespace App\Services\GuardianPortal;

use App\Models\Guardian;

class GuardianProtectedAccessState
{
    public function __construct(
        public readonly bool $hasAccessibleAccountState,
        public readonly bool $hasVerifiedEmail,
        public readonly bool $profileEligible,
        public readonly bool $hasLinkedStudents,
        public readonly bool $protectedEligible,
        public readonly ?Guardian $guardian,
        public readonly string $reason,
    ) {
    }
}
