<?php

namespace App\Data\Auth;

readonly class GoogleOAuthUser
{
    public function __construct(
        public string $subject,
        public ?string $email,
        public bool $emailVerified,
        public ?string $name = null,
        public ?string $avatar = null,
    ) {
    }
}
