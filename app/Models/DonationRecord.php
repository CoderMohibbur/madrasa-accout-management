<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationRecord extends Model
{
    use HasFactory;

    public const POSTING_PENDING = 'pending';
    public const POSTING_SKIPPED = 'skipped';
    public const POSTING_POSTED = 'posted';

    protected $fillable = [
        'donation_intent_id',
        'winning_payment_id',
        'user_id',
        'donor_id',
        'donation_category_id',
        'donor_mode',
        'display_mode',
        'amount',
        'currency',
        'donated_at',
        'posting_status',
        'name_snapshot',
        'email_snapshot',
        'phone_snapshot',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'donated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function donationIntent(): BelongsTo
    {
        return $this->belongsTo(DonationIntent::class);
    }

    public function winningPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'winning_payment_id');
    }

    public function donationCategory(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function resolvedDonationCategoryLabel(): ?string
    {
        $categoryLabel = $this->donationCategory?->displayLabel();

        if (is_string($categoryLabel) && $categoryLabel !== '') {
            return $categoryLabel;
        }

        $metadataLabel = data_get($this->metadata, 'category.label');

        if (is_string($metadataLabel) && $metadataLabel !== '') {
            return $metadataLabel;
        }

        return $this->donationIntent?->resolvedDonationCategoryLabel();
    }
}
