<x-layouts.app
    :title="'Pricing'"
    :description="'Honest, lead-based engagement tiers — from a two-week discovery sprint to an embedded product team. Every engagement is scoped to the work in front of it.'">

    {{-- ───────────────────────── Page header ───────────────────────── --}}
    <section class="frame relative pt-36 pb-16 md:pt-44 md:pb-20">
        <span class="tick left-6 top-32 md:left-10"></span>
        <span class="tick right-6 top-32 md:right-10"></span>

        <div class="max-w-3xl">
            <x-ui.eyebrow data-scramble>{{ content('pricing.hero_eyebrow', 'Pricing') }}</x-ui.eyebrow>

            <h1 class="display mt-6 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]" data-reveal data-reveal-delay="0.05">
                {!! nl2br(e(content('pricing.hero_title', "Engagements,\npriced honestly."))) !!}
            </h1>

            <p class="measure mt-7 text-[1.02rem] text-muted" data-reveal data-reveal-delay="0.18">
                {{ content('pricing.hero_intro', "We are a studio, not a checkout. Every engagement is scoped to the work in front of it — the numbers below are honest starting points, where most projects begin rather than where they are capped.") }}
            </p>
        </div>
    </section>

    {{-- ───────────────────────── Engagement tiers (carousel) ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between" data-reveal>
            <x-ui.eyebrow data-scramble>{{ content('pricing.tiers_eyebrow', 'Engagement tiers') }}</x-ui.eyebrow>
            <div class="flex items-center gap-5">
                <p class="hidden label-mono text-faint lg:block">{{ content('pricing.tiers_note', 'Lead-based · scoped per project · no checkout') }}</p>
                <div class="flex items-center gap-2.5">
                    <button type="button" class="p-prev inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors duration-300 hover:border-ink hover:bg-ink hover:text-paper" aria-label="Previous tier">
                        <x-ui.icon name="heroicon-m-arrow-left" class="h-4 w-4" />
                    </button>
                    <button type="button" class="p-next inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors duration-300 hover:border-ink hover:bg-ink hover:text-paper" aria-label="Next tier">
                        <x-ui.icon name="heroicon-m-arrow-right" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <p class="measure mt-6 text-[1rem] text-muted">{{ content('pricing.tiers_intro', 'Three ways to start, each scoped to the work in front of it — no packages, no checkout, no surprises.') }}</p>

        @if (collect($tiers)->isNotEmpty())
        <div class="mt-12" data-reveal>
            <div class="swiper pricing-swiper">
                <div class="swiper-wrapper">
                    @foreach ($tiers as $tier)
                        @php
                            $featured = $tier->is_featured;
                            $cardClass = $featured ? 'surface-dark border border-ink' : 'bg-paper border border-line';
                            $mutedClass = $featured ? 'text-paper/60' : 'text-muted';
                            $markClass = $featured ? 'text-paper' : 'text-faint';
                            $badgeClass = $featured ? 'border-paper/25 text-paper' : 'border-line text-muted';
                        @endphp
                        <div class="swiper-slide h-auto">
                            <div class="flex h-full flex-col {{ $cardClass }} p-10 md:p-12">
                                <div class="mb-8 flex h-5 items-center">
                                    @if ($featured)
                                        <span class="flex items-center gap-2 font-mono text-[0.62rem] uppercase tracking-[0.2em] text-paper">
                                            <span class="h-1.5 w-1.5 rounded-full bg-paper" aria-hidden="true"></span>Most popular
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="font-mono text-xl font-bold uppercase tracking-tight">{{ $tier->name }}</h2>
                                    <span class="shrink-0 border {{ $badgeClass }} px-2.5 py-1 font-mono text-[0.58rem] uppercase tracking-[0.16em]">{{ $tier->term }}</span>
                                </div>

                                <div class="mt-7 flex items-baseline gap-2">
                                    <span class="label-mono {{ $featured ? 'text-paper/55' : 'text-faint' }}">{{ $tier->price_label }}</span>
                                    <span class="display text-4xl md:text-5xl {{ $featured ? 'text-paper' : 'text-ink' }}">{{ $tier->price }}</span>
                                    @if ($tier->suffix)
                                        <span class="label-mono {{ $featured ? 'text-paper/55' : 'text-faint' }}">{{ $tier->suffix }}</span>
                                    @endif
                                </div>

                                <p class="mt-5 text-[0.97rem] leading-relaxed {{ $mutedClass }}">{{ $tier->tagline }}</p>

                                <div class="rule my-8"></div>

                                <ul class="space-y-3.5">
                                    @foreach ($tier->items ?? [] as $item)
                                        <li class="flex gap-3 text-[0.95rem] leading-snug">
                                            <span class="font-mono {{ $markClass }} select-none" aria-hidden="true">+</span>
                                            <span class="{{ $featured ? 'text-paper/85' : 'text-ink' }}">{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="mt-auto pt-10">
                                    <x-ui.button href="/start" :variant="$featured ? 'invert' : 'solid'" class="w-full justify-center">Start a project</x-ui.button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-pagination mt-10 flex items-center justify-center gap-2"></div>
        </div>
        @else
            <p class="mt-12 border border-line py-16 text-center font-mono text-sm uppercase tracking-widest text-faint">{{ content('pricing.tiers_empty', 'Engagement tiers are being finalised — check back soon.') }}</p>
        @endif

        @if (isset($services) && $services->isNotEmpty())
            <p class="mt-10 text-center label-mono text-faint">
                Every engagement draws on the full studio — {{ $services->pluck('title')->join(' · ') }}.
            </p>
        @endif
    </section>

    {{-- ───────────────────────── What's always included ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <x-ui.heading :eyebrow="content('pricing.included_eyebrow', 'No fine print')" :title="content('pricing.included_title', 'What\'s always included.')">
            {{ content('pricing.included_intro', 'However we work together, a few things never change — the reasons engagements stay honest.') }}
        </x-ui.heading>

        <div class="mt-14 grid border-l border-t border-line sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            @foreach ($included as $i => $inc)
                <div class="group border-b border-r border-line p-8 transition-colors duration-500 hover:bg-panel md:p-9" data-stagger-item>
                    <div class="display text-[2.6rem] leading-none text-faint transition-colors duration-500 group-hover:text-ink">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3 class="mt-7 font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $inc->label }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $inc->description }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ───────────────────────── FAQ ───────────────────────── --}}
    @if (isset($faqs) && $faqs->isNotEmpty())
        <section class="frame border-t border-line py-20 md:py-28">
            <x-ui.heading :eyebrow="content('pricing.faq_eyebrow', 'FAQ')" :title="content('pricing.faq_title', 'Questions, answered.')">
                {{ content('pricing.faq_intro', 'The questions we hear most, answered straight — before you ever send a brief.') }}
            </x-ui.heading>

            <div class="mx-auto mt-12 max-w-3xl border-t border-line" data-stagger>
                @foreach ($faqs as $faq)
                    <div x-data="faqItem" class="border-b border-line" data-stagger-item>
                        <button type="button" @click="toggle()"
                                class="group flex w-full items-center justify-between gap-6 py-6 text-left"
                                :aria-expanded="open" aria-controls="faq-panel-{{ $loop->index }}">
                            <span class="font-mono text-[0.95rem] font-bold uppercase tracking-tight transition-colors group-hover:text-ink/70">{{ $faq->question }}</span>
                            <span class="shrink-0 font-mono text-xl text-faint transition-transform duration-300" :class="iconClass" aria-hidden="true">+</span>
                        </button>
                        <div x-show="open" x-cloak class="faq-panel" id="faq-panel-{{ $loop->index }}">
                            <p class="measure pb-7 text-[0.97rem] leading-relaxed text-muted">{{ $faq->answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
