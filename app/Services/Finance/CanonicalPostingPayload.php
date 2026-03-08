<?php

namespace App\Services\Finance;

final class CanonicalPostingPayload
{
    public function __construct(
        public readonly string $typeKey,
        public readonly float $amount,
        public readonly float $debit,
        public readonly float $credit,
        public readonly string $direction,
        public readonly string $sourceType,
        public readonly ?int $sourceId = null,
        public readonly ?string $reference = null,
        public readonly array $metadata = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'type_key' => $this->typeKey,
            'amount' => $this->amount,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'direction' => $this->direction,
            'source_type' => $this->sourceType,
            'source_id' => $this->sourceId,
            'reference' => $this->reference,
            'metadata' => $this->metadata,
        ];
    }
}
