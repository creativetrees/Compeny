<x-layouts.app
    :title="'Services'"
    :description="'Strategy, design, and engineering under one roof — the capabilities we use to ship and scale digital products.'"
>
    {{-- ───────────────────────── Page header ───────────────────────── --}}
    <section class="frame relative pt-32 pb-16 md:pt-40 md:pb-20">
        <span class="tick left-6 top-28 md:left-10"></span>
        <span class="tick right-6 top-28 md:right-10"></span>

        <div class="max-w-4xl">
            <x-ui.eyebrow data-scramble>{{ content('services.hero_eyebrow', 'Services') }}</x-ui.eyebrow>

            <h1 class="display mt-7 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]">
                <span class="block" data-reveal data-reveal-delay="0.1">{{ content('services.hero_line1', 'Capabilities') }}</span>
                <span class="block" data-reveal data-reveal-delay="0.18">{{ content('services.hero_line2', 'that compound.') }}</span>
            </h1>

            <p class="mt-7 max-w-xl text-[1.05rem] text-muted" data-reveal data-reveal-delay="0.3">
                {{ content('services.hero_intro', "We keep strategy, design, and engineering under one roof. Each capability below stands on its own — and gets sharper the moment it's paired with the next.") }}
            </p>
        </div>
    </section>

    {{-- ──────────────────── The disciplines ──────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <div class="flex items-end justify-between gap-6">
            <x-ui.eyebrow plain>▪ {{ content('services.disciplines_eyebrow', 'The disciplines') }}</x-ui.eyebrow>
            <span class="hidden font-mono text-xs uppercase tracking-widest text-faint sm:inline-block">{{ content('services.disciplines_label', 'Pick one — or the full stack') }}</span>
        </div>

        {{-- Disciplines — clean capability cards --}}
        <div class="mt-12 grid border-l border-t border-line sm:grid-cols-2 md:mt-16" data-stagger>
            @forelse ($services as $service)
                <div class="group flex flex-col border-b border-r border-line p-8 transition-colors duration-500 hover:bg-panel md:p-10" data-stagger-item>
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center border border-line text-ink transition-colors duration-500 group-hover:border-ink group-hover:bg-ink group-hover:text-paper">
                            <x-ui.icon :name="$service->icon" class="h-5 w-5" />
                        </span>
                        <div class="flex items-center gap-3 pt-1">
                            @if ($service->is_featured)
                                <span class="inline-flex items-center gap-1.5 font-mono text-[0.55rem] uppercase tracking-widest text-faint"><span class="h-1.5 w-1.5 rounded-full bg-ink"></span>Featured</span>
                            @endif
                            <span class="label-mono text-faint">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    <h2 class="display mt-7 text-2xl leading-none sm:text-[1.7rem]">{{ $service->title }}</h2>
                    <p class="mt-4 text-[0.97rem] leading-relaxed text-muted">{{ $service->summary }}</p>

                    @if (! empty($service->capabilities))
                        <div class="mt-auto flex flex-wrap gap-2 border-t border-line pt-6">
                            @foreach ($service->capabilities as $cap)
                                <span class="border border-line px-2.5 py-1 font-mono text-[0.62rem] uppercase tracking-wide text-muted transition-colors duration-300 group-hover:border-ink/25 group-hover:text-ink">{{ $cap }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="border-b border-r border-line p-8 font-mono text-sm uppercase tracking-wide text-faint">Capabilities are being updated. Check back shortly.</p>
            @endforelse
        </div>
    </section>

</x-layouts.app>
