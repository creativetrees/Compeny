<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SanitizesRichHtml;

    /** @var array<int, string> */
    protected array $richHtml = ['summary', 'description'];

    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'slug', 'type', 'summary', 'description',
        'price_label', 'features', 'cover_path', 'cta_label', 'cta_url',
        'is_featured', 'status', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_featured' => 'boolean',
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
        return $query->orderBy('sort')->orderBy('title');
    }
}
