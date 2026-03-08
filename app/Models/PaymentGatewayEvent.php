<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGatewayEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'provider',
        'provider_event_id',
        'provider_order_id',
        'event_name',
        'request_source',
        'http_status',
        'signature_valid',
        'processing_status',
        'payload',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
        'http_status' => 'integer',
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
