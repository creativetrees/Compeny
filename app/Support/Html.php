<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;

class Html
{
    /** Tags allowed to survive sanitization (matches what the RichEditor produces). */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup',
        'ul', 'ol', 'li', 'a', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre', 'span',
    ];

    /** Tags removed together with their contents (everything else unknown is unwrapped). */
    private const STRIP_WITH_CONTENTS = ['script', 'style', 'iframe', 'object', 'embed', 'form'];

    /**
     * Sanitize admin-authored rich HTML (RichEditor output) so it is safe to render
     * on the public site with {!! !!}. Keeps a small formatting allowlist, removes
     * script/style/iframe (with contents), unwraps any other unknown tag, strips all
     * attributes except a safe <a href> (http/https/mailto/relative/anchor), and drops
     * on* handlers and javascript: URLs. Plain text (no "<") passes through untouched.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '' || ! str_contains($html, '<')) {
            return $html;
        }

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"?><body>'.$html.'</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);

        foreach (iterator_to_array($xpath->query('//*')) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->nodeName);

            if ($tag === 'body') {
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                if (in_array($tag, self::STRIP_WITH_CONTENTS, true)) {
                    $node->parentNode?->removeChild($node);
                } else {
                    $node->parentNode?->replaceChild($dom->createTextNode($node->textContent), $node);
                }

                continue;
            }

            foreach (iterator_to_array($node->attributes) as $attribute) {
                $name = strtolower($attribute->nodeName);
                $value = trim($attribute->nodeValue ?? '');

                $safeHref = $tag === 'a'
                    && $name === 'href'
                    && preg_match('#^(https?://|mailto:|/(?!/)|\#)#i', $value) === 1;

                if (! $safeHref) {
                    $node->removeAttribute($attribute->nodeName);
                }
            }

            if ($tag === 'a' && $node->hasAttribute('href')) {
                $node->setAttribute('rel', 'noopener nofollow');
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);

        if (! $body) {
            return '';
        }

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return $out;
    }

    /** Recursively sanitize every string leaf of a nested array (e.g. page_content). */
    public static function cleanDeep(array $data): array
    {
        array_walk_recursive($data, function (&$value): void {
            if (is_string($value)) {
                $value = static::clean($value);
            }
        });

        return $data;
    }
}
