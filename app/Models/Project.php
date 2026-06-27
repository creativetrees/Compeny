<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'slug', 'client_name', 'year', 'role', 'summary',
        'body', 'cover_path', 'gallery', 'services', 'results', 'website_url',
        'is_featured', 'status', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'services' => 'array',
            'results' => 'array',
            'is_featured' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        return str_starts_with($this->cover_path, 'http')
            ? $this->cover_path
            : Storage::url($this->cover_path);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('year');
    }
}
