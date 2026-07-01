<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use SanitizesRichHtml;

    /** @var array<int, string> */
    protected array $richHtml = ['quote'];

    use HasFactory;

    protected $fillable = [
        'project_id', 'author', 'role', 'company', 'quote', 'avatar_path',
        'rating', 'is_featured', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'rating' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return str_starts_with($this->avatar_path, 'http')
            ? $this->avatar_path
            : Storage::url($this->avatar_path);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort');
    }
}
