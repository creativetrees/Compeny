<?php

namespace App\Models;

use App\Models\Concerns\SanitizesRichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingInclude extends Model
{
    use SanitizesRichHtml;

    /** @var array<int, string> */
    protected array $richHtml = ['description'];

    use HasFactory;

    protected $fillable = [
        'label', 'description', 'sort',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
