<?php

namespace App\Models\Concerns;

use App\Support\Html;

/**
 * Sanitizes RichEditor-backed fields on every save (forms, seeders, tinker, and
 * public input like Lead.message), so stored HTML is always safe to render with
 * {!! !!}. A compromised admin — or a hostile public submission — cannot persist
 * script tags, iframes, event handlers, or javascript URLs through these fields.
 *
 * Each model lists its rich fields:  protected array $richHtml = ['body', 'summary'];
 */
trait SanitizesRichHtml
{
    protected static function bootSanitizesRichHtml(): void
    {
        static::saving(function ($model): void {
            foreach ($model->richHtmlFields() as $field) {
                if (is_string($model->{$field} ?? null)) {
                    $model->{$field} = Html::clean($model->{$field});
                }
            }
        });
    }

    /** @return array<int, string> */
    public function richHtmlFields(): array
    {
        return $this->richHtml ?? [];
    }
}
