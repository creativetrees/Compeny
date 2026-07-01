<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeamMember extends Model
{
    use SanitizesRichHtml;

    /** @var array<int, string> */
    protected array $richHtml = ['bio'];

    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'role', 'bio', 'photo_path', 'socials', 'is_published', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'socials' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return str_starts_with($this->photo_path, 'http')
            ? $this->photo_path
            : Storage::url($this->photo_path);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
    }
}
