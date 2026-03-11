<?php

namespace App\Services\Donations;

use Illuminate\Support\Str;

class DonationReferenceGenerator
{
    public function publicReference(): string
    {
        return 'DON-'.Str::upper((string) Str::ulid());
    }

    public function accessKey(): string
    {
        return Str::random(48);
    }

    public function accessKeyHash(string $accessKey): string
    {
        return hash('sha256', $accessKey);
    }

    public function accessKeyMatches(?string $storedHash, ?string $plainAccessKey): bool
    {
        if (! $storedHash || ! $plainAccessKey) {
            return false;
        }

        return hash_equals($storedHash, $this->accessKeyHash($plainAccessKey));
    }
}
