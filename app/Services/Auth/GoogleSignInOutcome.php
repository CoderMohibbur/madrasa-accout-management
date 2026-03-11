<?php

namespace App\Services\Auth;

use App\Models\User;

readonly class GoogleSignInOutcome
{
    public function __construct(
        public ?User $user,
        public string $status,
        public string $message,
        public bool $shouldLogin = false,
    ) {
    }

    public function failed(): bool
    {
        return ! in_array($this->status, [
            'created_public_user',
            'created_donor_user',
            'linked_existing_user',
            'existing_identity',
            'linked_authenticated_user',
        ], true);
    }
}
