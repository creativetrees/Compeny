<div class="ct2fa">
    {{-- Scoped styles. The panel CSS is purged, so Tailwind utilities aren't compiled
         for this component — it ships its own semantic classes (ct2fa-* prefix). Colours
         are built from color-mix(currentColor …) so they adapt to both the light and dark
         zinc themes; the QR tile stays fixed white so any scanner reads it. wire:ignore
         stops Livewire re-morphing the <style> on every update. --}}
    <style wire:ignore>
        .ct2fa {
            --ct-success: #16a34a;
            --ct-amber: #d97706;
            --ct-danger: #dc2626;
            --ct-border: color-mix(in srgb, currentColor 12%, transparent);
            --ct-border-strong: color-mix(in srgb, currentColor 22%, transparent);
            --ct-surface: color-mix(in srgb, currentColor 4%, transparent);
            --ct-surface-2: color-mix(in srgb, currentColor 7%, transparent);
            --ct-muted: color-mix(in srgb, currentColor 56%, transparent);
            --ct-faint: color-mix(in srgb, currentColor 34%, transparent);
            --ct-mono: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
        }
        .ct2fa [x-cloak] { display: none !important; }

        /* ── Step 1 · scan ─────────────────────────────────────────── */
        .ct2fa-scan { display: grid; gap: 1.4rem; }
        @media (min-width: 768px) {
            .ct2fa-scan { grid-template-columns: auto minmax(0, 1fr); align-items: stretch; }
        }

        .ct2fa-tilewrap { display: flex; flex-direction: column; align-items: center; gap: .7rem; }
        .ct2fa-tile {
            position: relative; background: #fff; padding: .7rem; border-radius: 1.1rem;
            box-shadow: 0 14px 34px -16px rgba(0, 0, 0, .55), 0 0 0 1px color-mix(in srgb, currentColor 9%, transparent);
        }
        /* Corner ticks — a credential-card cue framing the code. */
        .ct2fa-tile::before, .ct2fa-tile::after {
            content: ""; position: absolute; width: .85rem; height: .85rem; pointer-events: none;
            border: 2px solid color-mix(in srgb, var(--ct-success) 65%, transparent);
        }
        .ct2fa-tile::before { top: .3rem; left: .3rem; border-right: 0; border-bottom: 0; border-top-left-radius: .4rem; }
        .ct2fa-tile::after { bottom: .3rem; right: .3rem; border-left: 0; border-top: 0; border-bottom-right-radius: .4rem; }
        .ct2fa-qr { display: block; width: 208px; height: 208px; max-width: 56vw; border-radius: .4rem; }
        .ct2fa-tilehint {
            display: inline-flex; align-items: center; gap: .35rem; font-size: .73rem; font-weight: 600;
            letter-spacing: .02em; color: var(--ct-muted);
        }

        .ct2fa-manual {
            min-width: 0; display: flex; flex-direction: column; justify-content: center; gap: .55rem;
            padding: 1rem 1.15rem; border: 1px solid var(--ct-border); border-radius: 1rem; background: var(--ct-surface);
        }
        .ct2fa-step { display: flex; align-items: center; gap: .6rem; }
        .ct2fa-num {
            flex: none; width: 1.4rem; height: 1.4rem; border-radius: 50%; display: grid; place-items: center;
            font-size: .72rem; font-weight: 700; font-variant-numeric: tabular-nums;
            color: color-mix(in srgb, var(--ct-success) 90%, currentColor);
            background: color-mix(in srgb, var(--ct-success) 14%, transparent);
            border: 1px solid color-mix(in srgb, var(--ct-success) 32%, transparent);
        }
        .ct2fa-steptxt { font-size: .82rem; line-height: 1.45; color: var(--ct-muted); }
        .ct2fa-steptxt b { color: inherit; font-weight: 600; }

        .ct2fa-manlabel {
            text-transform: uppercase; letter-spacing: .13em; font-size: .64rem; font-weight: 700;
            color: var(--ct-faint); margin: .35rem 0 0;
        }
        .ct2fa-keyrow { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
        .ct2fa-key {
            flex: 1 1 11rem; min-width: 0; font-family: var(--ct-mono); font-size: .9rem; font-weight: 600;
            letter-spacing: .12em; padding: .55rem .7rem; border-radius: .6rem; word-break: break-all;
            background: color-mix(in srgb, currentColor 6%, transparent); border: 1px solid var(--ct-border);
        }

        /* ── Step 2 · recovery codes ───────────────────────────────── */
        .ct2fa-codeshead {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .65rem;
            margin-bottom: 1rem;
        }
        .ct2fa-codestitle { display: flex; align-items: center; gap: .55rem; font-size: .9rem; font-weight: 600; }
        .ct2fa-chip {
            display: inline-flex; align-items: center; gap: .25rem; padding: .14rem .5rem; border-radius: 999px;
            font-size: .64rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
            color: color-mix(in srgb, var(--ct-amber) 88%, currentColor);
            background: color-mix(in srgb, var(--ct-amber) 14%, transparent);
            border: 1px solid color-mix(in srgb, var(--ct-amber) 30%, transparent);
        }

        .ct2fa-codes { display: grid; grid-template-columns: 1fr; gap: .5rem; }
        @media (min-width: 560px) { .ct2fa-codes { grid-template-columns: 1fr 1fr; } }
        .ct2fa-code {
            display: flex; align-items: center; gap: .55rem; padding: .3rem .3rem .3rem .55rem;
            border-radius: .6rem; border: 1px solid var(--ct-border); background: var(--ct-surface-2);
        }
        .ct2fa-codeidx {
            flex: none; font-family: var(--ct-mono); font-size: .68rem; font-weight: 700; width: 1.15rem;
            text-align: right; color: var(--ct-faint); font-variant-numeric: tabular-nums;
        }
        .ct2fa-codeval { flex: 1 1 auto; font-family: var(--ct-mono); font-size: .86rem; letter-spacing: .04em; }
        .ct2fa-copy {
            flex: none; display: grid; place-items: center; width: 1.95rem; height: 1.95rem; border: 0;
            border-radius: .45rem; cursor: pointer; color: var(--ct-muted); background: transparent;
            transition: background .15s ease, color .15s ease;
        }
        .ct2fa-copy:hover { background: color-mix(in srgb, currentColor 10%, transparent); color: inherit; }
        .ct2fa-copy:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, currentColor 18%, transparent); }
        .ct2fa-codesfoot { display: flex; justify-content: flex-end; margin-top: 1rem; }

        /* ── Enabled state ─────────────────────────────────────────── */
        .ct2fa-enabled { display: flex; flex-direction: column; gap: 1.25rem; }
        .ct2fa-meter {
            display: flex; align-items: center; gap: .85rem; padding: .95rem 1.1rem; border-radius: .9rem;
            border: 1px solid color-mix(in srgb, var(--ct-success) 28%, transparent);
            background: color-mix(in srgb, var(--ct-success) 8%, transparent);
        }
        .ct2fa-metericon {
            flex: none; width: 2.4rem; height: 2.4rem; border-radius: .7rem; display: grid; place-items: center;
            color: var(--ct-success);
            background: color-mix(in srgb, var(--ct-success) 15%, transparent);
            border: 1px solid color-mix(in srgb, var(--ct-success) 30%, transparent);
        }
        .ct2fa-metercount { font-size: 1.4rem; font-weight: 700; line-height: 1; font-variant-numeric: tabular-nums; }
        .ct2fa-meterlabel { font-size: .8rem; color: var(--ct-muted); margin-top: .2rem; }
        .ct2fa-enabledactions { display: flex; flex-wrap: wrap; gap: .6rem; }

        @media (prefers-reduced-motion: reduce) {
            .ct2fa-copy { transition: none; }
        }
    </style>

    {{ $this->form }}

    <x-filament-actions::modals />
</div>
