<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'slug',
        'name',
        'label',
        'description',
        'badge',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    public function displayLabel(): string
    {
        return $this->label ?: $this->name;
    }

    public function toPublicFormOption(): array
    {
        return [
            'id' => $this->getKey(),
            'key' => $this->key,
            'slug' => $this->slug,
            'name' => $this->name,
            'label' => $this->displayLabel(),
            'description' => $this->description,
            'badge' => $this->badge,
            'featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
