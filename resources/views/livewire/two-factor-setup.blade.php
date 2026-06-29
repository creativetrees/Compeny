<div class="ct2fa">
    {{-- Scoped styles. The panel CSS is purged, so Tailwind utilities aren't compiled
         for this component — it ships its own semantic classes (ct2fa-* prefix). Surfaces
         are built from color-mix(currentColor …) so they adapt to both the light and dark
         zinc themes; the orange CTA + the white QR tile stay fixed so the design reads the
         same everywhere and any scanner can read the code. The structural chrome (the
         card, the two-column grid, the headings) is a native Filament Section, so the tab
         fills the width and stacks on mobile exactly like the rest of the form.
         wire:ignore stops Livewire re-morphing the <style> on every update. --}}
    <style wire:ignore>
        .ct2fa {
            --ct-orange: #f97316;
            --ct-orange-strong: #ea580c;
            --ct-success: #16a34a;
            --ct-danger: #dc2626;
            --ct-border: color-mix(in srgb, currentColor 12%, transparent);
            --ct-surface: color-mix(in srgb, currentColor 5%, transparent);
            --ct-surface-2: color-mix(in srgb, currentColor 8%, transparent);
            --ct-muted: color-mix(in srgb, currentColor 66%, transparent);
            --ct-mono: ui-monospace, "SF Mono", SFMono-Regular, Menlo, Consolas, monospace;
            display: block;
        }
        .ct2fa [x-cloak] { display: none !important; }
        .ct2fa-sr {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }

        /* ── Sub-section header (inside each grid column) ──────────── */
        .ct2fa-block { display: flex; flex-direction: column; height: 100%; }
        .ct2fa-head { display: flex; align-items: center; gap: .55rem; }
        .ct2fa-headicon {
            flex: none; display: grid; place-items: center; width: 1.85rem; height: 1.85rem;
            border-radius: .55rem; color: var(--ct-orange);
            background: color-mix(in srgb, var(--ct-orange) 13%, transparent);
        }
        .ct2fa-title { font-size: .95rem; font-weight: 600; line-height: 1.25; }
        .ct2fa-sub { font-size: .82rem; line-height: 1.5; color: var(--ct-muted); margin: .45rem 0 0; }

        /* ── Screen 1 · scan card (left column) ────────────────────── */
        .ct2fa-card {
            margin-top: .95rem; padding: 1.15rem; border: 1px solid var(--ct-border);
            border-radius: .9rem; background: var(--ct-surface);
        }
        /* Mobile: QR centred on top, key below. Desktop (≥lg): side-by-side. */
        .ct2fa-cardgrid { display: flex; flex-direction: column; gap: 1.1rem; }
        .ct2fa-qrtile {
            align-self: center; flex: none; background: #fff; padding: .55rem; border-radius: .8rem;
            box-shadow: 0 0 0 1px color-mix(in srgb, currentColor 10%, transparent);
        }
        .ct2fa-qr { display: block; width: 150px; height: 150px; max-width: 56vw; border-radius: .35rem; }
        .ct2fa-manual { min-width: 0; display: flex; flex-direction: column; gap: .55rem; }
        @media (min-width: 1024px) {
            .ct2fa-cardgrid { flex-direction: row; align-items: center; gap: 1.3rem; }
            .ct2fa-qrtile { align-self: auto; }
            .ct2fa-manual { flex: 1 1 auto; }
        }

        .ct2fa-manlabel { font-size: .8rem; line-height: 1.45; color: var(--ct-muted); }
        .ct2fa-key {
            font-family: var(--ct-mono); font-size: .92rem; font-weight: 600; letter-spacing: .08em;
            padding: .6rem .75rem; border-radius: .55rem; word-break: break-all;
            background: var(--ct-surface-2); border: 1px solid var(--ct-border);
        }

        /* ── Buttons ───────────────────────────────────────────────── */
        .ct2fa-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
            font-size: .875rem; font-weight: 600; line-height: 1; padding: .65rem 1.15rem;
            border-radius: .6rem; border: 1px solid transparent; cursor: pointer;
            transition: background .15s ease, border-color .15s ease, opacity .15s ease;
        }
        .ct2fa-btn:disabled { opacity: .55; cursor: not-allowed; }
        .ct2fa-btn--cta { background: var(--ct-orange); color: #fff; }
        .ct2fa-btn--cta:hover:not(:disabled) { background: var(--ct-orange-strong); }
        .ct2fa-btn--cta:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ct-orange); }
        .ct2fa-btn--ghost { background: var(--ct-surface-2); border-color: var(--ct-border); color: inherit; }
        .ct2fa-btn--ghost:hover:not(:disabled) { background: color-mix(in srgb, currentColor 11%, transparent); }
        .ct2fa-btn--ghost:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }
        .ct2fa-btn--sm { padding: .5rem .8rem; font-size: .8rem; border-radius: .5rem; }
        .ct2fa-btn--full { width: 100%; padding: .82rem 1.15rem; font-size: .95rem; }
        .ct2fa-copycode { align-self: flex-start; }
        .ct2fa-spin { animation: ct2fa-spin .8s linear infinite; }
        @keyframes ct2fa-spin { to { transform: rotate(360deg); } }

        /* ── Action bar (full Section width, below the columns) ────── */
        .ct2fa-actionbar {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end;
            gap: .65rem; margin-top: 1.4rem; padding-top: 1.25rem;
            border-top: 1px solid var(--ct-border);
        }

        /* ── Screen 2 · backup codes ───────────────────────────────── */
        .ct2fa-codeswrap { max-width: 40rem; margin-inline: auto; }
        .ct2fa-codes { display: grid; grid-template-columns: 1fr; gap: .6rem; }
        @media (min-width: 480px) { .ct2fa-codes { grid-template-columns: 1fr 1fr; } }
        .ct2fa-code {
            display: flex; align-items: center; gap: .5rem; padding: .5rem .5rem .5rem .85rem;
            border: 1px solid var(--ct-border); border-radius: .6rem; background: var(--ct-surface-2);
        }
        .ct2fa-codeval { flex: 1 1 auto; min-width: 0; font-family: var(--ct-mono); font-size: .9rem; font-weight: 600; letter-spacing: .05em; }
        .ct2fa-copy {
            flex: none; display: grid; place-items: center; width: 2.5rem; height: 2.5rem; border: 0;
            border-radius: .5rem; cursor: pointer; color: var(--ct-muted); background: transparent;
            transition: background .15s ease, color .15s ease;
        }
        .ct2fa-copy:hover { background: color-mix(in srgb, currentColor 10%, transparent); color: inherit; }
        .ct2fa-copy:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }
        .ct2fa-codesfoot { margin-top: 1.2rem; }

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

        @media (prefers-reduced-motion: reduce) {
            .ct2fa-btn, .ct2fa-copy { transition: none; }
            .ct2fa-spin { animation: none; }
        }
    </style>

    {{ $this->form }}

    <x-filament-actions::modals />
</div>
