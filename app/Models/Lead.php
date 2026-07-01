<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use SanitizesRichHtml;

    /** @var array<int, string> */
    protected array $richHtml = ['message'];

    use HasFactory;

    public const STATUSES = ['new', 'contacted', 'qualified', 'won', 'lost'];

    protected $fillable = [
        'name', 'email', 'company', 'phone', 'budget', 'service_interest',
        'message', 'status', 'source', 'meta',
    ];

    protected function casts(): array
    {
        return ['meta' => 'array'];
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
