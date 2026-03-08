<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'fees_type_id',
        'user_id',
        'portal_enabled',
        'address',
        'notes',
        'isActived',
        'isDeleted',
    ];

    protected $casts = [
        'portal_enabled' => 'boolean',
        'isActived' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    public function AddFess()
    {
        return $this->belongsTo(AddFessType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

