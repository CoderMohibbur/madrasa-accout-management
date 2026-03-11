<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalIdentity extends Model
{
    use HasFactory;

    public const PROVIDER_GOOGLE = 'google';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_subject',
        'provider_email',
        'provider_email_verified',
        'linked_at',
        'last_used_at',
    ];

    protected $casts = [
        'provider_email_verified' => 'boolean',
        'linked_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
