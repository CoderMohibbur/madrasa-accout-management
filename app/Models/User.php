<?php

namespace App\Models;

use App\Models\Concerns\HasRoles;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    public const ROLE_REGISTERED_USER = 'registered_user';

    public const APPROVAL_NOT_REQUIRED = 'approval_not_required';
    public const APPROVAL_PENDING = 'approval_pending';
    public const APPROVAL_APPROVED = 'approval_approved';
    public const APPROVAL_SUSPENDED = 'approval_suspended';

    public const ACCOUNT_STATUS_ACTIVE = 'active';
    public const ACCOUNT_STATUS_INACTIVE = 'inactive';
    public const ACCOUNT_STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'approval_status',
        'account_status',
        'phone',
        'phone_verified_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'deleted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null ? null : Str::lower(trim($value)),
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => PhoneNumber::normalize($value),
        );
    }

    public function externalIdentities(): HasMany
    {
        return $this->hasMany(ExternalIdentity::class);
    }

    public function googleIdentity(): HasOne
    {
        return $this->hasOne(ExternalIdentity::class)->where('provider', ExternalIdentity::PROVIDER_GOOGLE);
    }

    public function resolvedApprovalStatus(): string
    {
        $status = trim((string) $this->approval_status);

        if ($status !== '') {
            return $status;
        }

        return $this->hasVerifiedEmail()
            ? self::APPROVAL_APPROVED
            : self::APPROVAL_PENDING;
    }

    public function resolvedAccountStatus(): string
    {
        $status = trim((string) $this->account_status);

        if ($status !== '') {
            return $status;
        }

        return self::ACCOUNT_STATUS_ACTIVE;
    }

    public function hasApprovedAccountAccess(): bool
    {
        return in_array($this->resolvedApprovalStatus(), [
            self::APPROVAL_NOT_REQUIRED,
            self::APPROVAL_APPROVED,
        ], true);
    }

    public function hasActiveAccountStatus(): bool
    {
        return $this->resolvedAccountStatus() === self::ACCOUNT_STATUS_ACTIVE;
    }

    public function isAccountDeleted(): bool
    {
        return ! is_null($this->deleted_at);
    }

    public function hasVerifiedPhone(): bool
    {
        return $this->normalizedPhone() !== null && ! is_null($this->phone_verified_at);
    }

    public function normalizedPhone(): ?string
    {
        return PhoneNumber::normalize($this->phone);
    }

    public function maskedPhone(): ?string
    {
        return PhoneNumber::mask($this->phone);
    }

    public function hasAccessibleAccountState(): bool
    {
        return $this->hasApprovedAccountAccess()
            && $this->hasActiveAccountStatus()
            && ! $this->isAccountDeleted();
    }
}
