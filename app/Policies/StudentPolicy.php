<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('management')) {
            return true;
        }

        $guardian = $user->guardianProfile;

        if (! $guardian || ! $guardian->portal_enabled || ! $guardian->isActived || $guardian->isDeleted) {
            return false;
        }

        return $guardian->students()->whereKey($student->getKey())->exists();
    }
}
