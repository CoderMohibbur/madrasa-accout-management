<?php

namespace App\Services\Auth;

use App\Models\AuditLog;
use App\Models\User;

class ContactVerificationAuditLogger
{
    public function record(
        ?User $actor,
        User $target,
        string $event,
        string $summary,
        array $before = [],
        array $after = [],
        array $context = [],
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_user_id' => $actor?->getKey(),
            'auditable_type' => $target::class,
            'auditable_id' => $target->getKey(),
            'event' => $event,
            'summary' => $summary,
            'before' => $before === [] ? null : $before,
            'after' => $after === [] ? null : $after,
            'context' => $context === [] ? null : $context,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
