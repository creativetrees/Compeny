<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'lead', 'body', 'deliverables', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'deliverables' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
