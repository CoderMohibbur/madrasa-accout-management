<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'address',
        'notes',
        'portal_enabled',
        'isActived',
        'isDeleted',
    ];

    protected $casts = [
        'portal_enabled' => 'boolean',
        'isActived' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['relationship_label', 'is_primary', 'notes'])
            ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(StudentFeeInvoice::class);
    }
}
