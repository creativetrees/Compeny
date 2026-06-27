<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = ['group', 'key', 'label', 'value', 'sort'];

    /** Per-request cache of all key => value pairs. */
    protected static ?array $store = null;

    /**
     * Resolve an editable copy string by key, falling back to $default
     * (the original hardcoded text) when missing or empty.
     */
    public static function value(string $key, string $default = ''): string
    {
        if (static::$store === null) {
            try {
                static::$store = static::query()->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                static::$store = [];
            }
        }

        $value = static::$store[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function flushCache(): void
    {
        static::$store = null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group')->orderBy('sort')->orderBy('id');
    }
}
