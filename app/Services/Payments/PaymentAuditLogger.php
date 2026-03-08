<?php

namespace App\Services\Payments;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentAuditLogger
{
    public function record(
        ?User $actor,
        Model $auditable,
        string $event,
        string $summary,
        array $before = [],
        array $after = [],
        array $context = [],
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_user_id' => $actor?->getKey(),
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
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
