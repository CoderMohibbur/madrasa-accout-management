<?php

namespace App\Services\Portal;

class ExternalAdmissionUrlResolver
{
    private const DISALLOWED_INTERNAL_PATH_PREFIXES = [
        '/dashboard',
        '/donor',
        '/guardian',
        '/management',
        '/payments',
    ];

    public function resolve(): ?string
    {
        $url = trim((string) config('portal.admission.external_url'));

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);

        if (($parts['scheme'] ?? null) !== 'https' || blank($parts['host'] ?? null)) {
            return null;
        }

        if ($this->matchesProtectedInternalPath($parts)) {
            return null;
        }

        return $url;
    }

    private function matchesProtectedInternalPath(array $parts): bool
    {
        $appParts = parse_url((string) config('app.url'));

        if (! is_array($appParts) || blank($appParts['host'] ?? null) || ($appParts['host'] ?? null) !== ($parts['host'] ?? null)) {
            return false;
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');

        foreach (self::DISALLOWED_INTERNAL_PATH_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }
}
