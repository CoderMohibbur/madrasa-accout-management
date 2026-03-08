<?php

namespace App\Services\Finance;

use App\Support\TransactionLedger;
use InvalidArgumentException;

class CanonicalPostingService
{
    public function build(
        string $typeKey,
        float $amount,
        string $sourceType,
        ?int $sourceId = null,
        ?string $reference = null,
        array $metadata = [],
    ): CanonicalPostingPayload {
        $normalizedSourceType = trim($sourceType);

        if ($normalizedSourceType === '') {
            throw new InvalidArgumentException('Source type is required for canonical posting.');
        }

        $split = TransactionLedger::split($typeKey, $amount);

        return new CanonicalPostingPayload(
            typeKey: $typeKey,
            amount: $amount,
            debit: (float) $split['debit'],
            credit: (float) $split['credit'],
            direction: $split['credit'] > 0 ? 'inflow' : 'outflow',
            sourceType: $normalizedSourceType,
            sourceId: $sourceId,
            reference: $reference,
            metadata: $metadata,
        );
    }
}
