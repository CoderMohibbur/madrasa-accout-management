<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use App\Models\Transactions;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_REDIRECT_PENDING = 'redirect_pending';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_AWAITING_MANUAL_PAYMENT = 'awaiting_manual_payment';
    public const STATUS_MANUAL_REVIEW = 'manual_review';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_VERIFIED = 'verified';
    public const VERIFICATION_FAILED = 'failed';
    public const VERIFICATION_MANUAL_REVIEW = 'manual_review';

    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REDIRECT_PENDING,
        self::STATUS_PENDING_VERIFICATION,
        self::STATUS_AWAITING_MANUAL_PAYMENT,
        self::STATUS_MANUAL_REVIEW,
    ];

    protected $fillable = [
        'user_id',
        'reviewed_by_user_id',
        'payable_type',
        'payable_id',
        'posted_transaction_id',
        'status',
        'verification_status',
        'status_reason',
        'provider',
        'provider_mode',
        'currency',
        'amount',
        'idempotency_key',
        'provider_reference',
        'metadata',
        'initiated_at',
        'paid_at',
        'verified_at',
        'failed_at',
        'cancelled_at',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'initiated_at' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'failed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function gatewayEvents(): HasMany
    {
        return $this->hasMany(PaymentGatewayEvent::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function postedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'posted_transaction_id');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [
            self::STATUS_PAID,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function setStatusReasonAttribute($value): void
    {
        $this->attributes['status_reason'] = filled($value)
            ? Str::limit((string) $value, 255, '')
            : null;
    }
}
