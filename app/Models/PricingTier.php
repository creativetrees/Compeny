<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'term', 'price_label', 'price', 'suffix', 'tagline', 'items', 'is_featured', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
