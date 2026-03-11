<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DonationIntent extends Model
{
    use HasFactory;

    public const DONOR_MODE_GUEST = 'guest';
    public const DONOR_MODE_IDENTIFIED = 'identified';

    public const DISPLAY_MODE_IDENTIFIED = 'identified';
    public const DISPLAY_MODE_ANONYMOUS = 'anonymous_display';

    public const STATUS_OPEN = 'open';
    public const STATUS_MANUAL_REVIEW = 'manual_review';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'donor_id',
        'donor_mode',
        'display_mode',
        'amount',
        'currency',
        'status',
        'public_reference',
        'guest_access_token_hash',
        'name_snapshot',
        'email_snapshot',
        'phone_snapshot',
        'metadata',
        'expires_at',
        'settled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function latestPayment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }

    public function donationRecord(): HasOne
    {
        return $this->hasOne(DonationRecord::class);
    }

    public function isSettled(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }
}
