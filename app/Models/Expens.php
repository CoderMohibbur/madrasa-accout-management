<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expens extends Model
{
    use HasFactory;

    protected $table = 'expens';

    protected $fillable = [
        'name',
        'catagory_id',
        'isActived',
        'isDeleted',
    ];

    protected $casts = [
        'isActived' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    public function scopeNotDeleted($q)
    {
        return $q->where('isDeleted', false);
    }

    public function scopeActive($q)
    {
        return $q->where('isActived', true)->where('isDeleted', false);
    }
}
