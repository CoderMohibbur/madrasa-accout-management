<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Services\GuardianPortal\GuardianPortalData;

class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        if (! $user->hasAccessibleAccountState()) {
            return false;
        }

        if ($user->hasRole('management')) {
            return true;
        }

        $access = app(GuardianPortalData::class)->resolveAccess($user);
        $guardian = $access->guardian;

        if (! $access->protectedEligible || ! $guardian) {
            return false;
        }

        return $guardian->students()->whereKey($student->getKey())->exists();
    }
}
