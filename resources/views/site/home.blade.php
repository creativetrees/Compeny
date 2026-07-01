@php
    // Hero title is a RichEditor field. Turn block boundaries (</p>, <br>, …) into
    // line breaks, strip inline tags (the scramble headline animates plain text),
    // and split into the per-line spans the headline renders.
    $heroTitleRaw = filled($settings->hero_title) ? $settings->hero_title : "WE GROW DIGITAL\nPRODUCTS THAT SCALE";
    $heroTitleText = html_entity_decode(strip_tags(preg_replace(
        ['#</(?:p|div|h[1-6]|li)>#i', '#<br\s*/?>#i'],
        "\n",
        $heroTitleRaw
    )), ENT_QUOTES);
    $titleLines = array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n/', $heroTitleText)),
        fn ($line) => $line !== ''
    ));
    if (empty($titleLines)) {
        $titleLines = ['WE GROW DIGITAL', 'PRODUCTS THAT SCALE'];
    }
@endphp

<x-layouts.app>
    {{-- ───────────────────────── Hero ───────────────────────── --}}
    <section class="relative overflow-hidden">
        <div data-charfield class="charfield" aria-hidden="true"></div>

        <div class="frame relative pt-36 pb-16 md:pt-44 md:pb-24">
            <span class="tick left-6 top-28 md:left-10"></span>
            <span class="tick right-6 top-28 md:right-10"></span>

            <div class="text-center">
                <div class="mb-8 flex justify-center">
                    <x-ui.eyebrow data-scramble>{{ $settings->hero_eyebrow ?? 'Digital product studio & IT ecosystem' }} ›</x-ui.eyebrow>
                </div>

                <h1 class="display hero-headline leading-[0.92]">
                    @foreach ($titleLines as $i => $line)
                        <span class="block" data-scramble data-scramble-duration="1100" data-scramble-delay="{{ $i * 220 }}">{{ $line }}</span>
                    @endforeach
                </h1>

                <div class="measure mx-auto mt-8 text-[1rem] text-muted sm:text-[1.05rem] richtext" data-reveal data-reveal-delay="0.3">
                    {!! filled($settings->hero_subtitle) ? $settings->hero_subtitle : 'We help startups and teams turn ideas into powerful digital products — from strategy and design to scalable engineering.' !!}
                </div>

                <div class="mt-9 flex items-center justify-center gap-3" data-reveal data-reveal-delay="0.4">
                    <x-ui.button href="{{ $settings->hero_cta_url ?: '/start' }}">{{ $settings->hero_cta_label ?: 'Start a project' }}</x-ui.button>
                    @if (filled($settings->hero_cta_secondary_label ?? 'View work'))
                        <x-ui.button href="{{ $settings->hero_cta_secondary_url ?: '/work' }}" variant="ghost" :magnetic="false">{{ $settings->hero_cta_secondary_label ?: 'View work' }}</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ──────────────────── Trusted by ──────────────────── --}}
    @if ($clients->isNotEmpty())
        <section class="frame border-t border-line py-12">
            <div class="mb-8 flex justify-center">
                <x-ui.eyebrow plain>▪ {{ content('home.trusted_eyebrow', 'Trusted by innovative teams') }}</x-ui.eyebrow>
            </div>
            <x-ui.marquee :clients="$clients" />
        </section>
    @endif

    {{-- ──────────────────── Services ──────────────────── --}}
    @if ($services->isNotEmpty())
        <section id="services" class="frame border-t border-line py-20 md:py-28">
            <x-ui.heading :eyebrow="content('home.cap_eyebrow', 'Capabilities')" :title="content_rich('home.cap_title', 'Everything you need to launch and scale.')">
                {!! content_rich('home.cap_intro', 'One embedded team across strategy, design, and engineering — so nothing is lost in handoff.') !!}
            </x-ui.heading>

            <div class="mt-14 grid border-l border-t border-line sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($services as $service)
                    <a href="/services"
                       class="group relative flex flex-col border-b border-r border-line p-7 transition-colors duration-500 hover:bg-panel md:p-8"
                       data-stagger-item data-cursor-grow
                       aria-label="{{ $service->title }} — see services">
                        {{-- icon + catalog index --}}
                        <div class="flex items-start justify-between">
                            <span class="inline-flex h-12 w-12 items-center justify-center border border-line text-ink transition-colors duration-500 group-hover:border-ink group-hover:bg-ink group-hover:text-paper">
                                <x-ui.icon :name="$service->icon" class="h-5 w-5" />
                            </span>
                            <span class="label-mono text-faint">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        {{-- title + reveal arrow --}}
                        <h3 class="mt-7 flex items-center gap-2 font-mono text-[1.05rem] font-bold uppercase tracking-tight">
                            {{ $service->title }}
                            <span class="-translate-x-1.5 text-muted opacity-0 transition-all duration-500 group-hover:translate-x-0 group-hover:opacity-100" aria-hidden="true">→</span>
                        </h3>

                        <p class="mt-3 text-sm leading-relaxed text-muted">{!! rich_html($service->summary) !!}</p>

                        @if (! empty($service->capabilities))
                            <div class="mt-auto flex flex-wrap gap-2 border-t border-line pt-6">
                                @foreach ($service->capabilities as $cap)
                                    <span class="border border-line px-2.5 py-1 font-mono text-[0.62rem] uppercase tracking-wide text-muted transition-colors duration-300 group-hover:border-ink/25 group-hover:text-ink">{{ $cap }}</span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ──────────────────── Selected work ──────────────────── --}}
    @if ($projects->isNotEmpty())
        <section id="work" class="frame border-t border-line py-20 md:py-28">
            <div class="flex items-end justify-between gap-6">
                <x-ui.heading :eyebrow="content('home.work_eyebrow', 'Selected work')" :title="content_rich('home.work_title', 'Proof, not promises.')">
                    {!! content_rich('home.work_intro', "A selection of products we've designed, built, and shipped — and the outcomes that followed.") !!}
                </x-ui.heading>
                <a href="/work" class="link-underline hidden shrink-0 font-mono text-xs uppercase tracking-widest sm:inline-block">{{ content('home.work_link', 'All work →') }}</a>
            </div>

            <div class="mt-14 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($projects as $project)
                    <x-ui.project-card :project="$project" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ──────────────────── Process ──────────────────── --}}
    <section id="process" class="frame border-t border-line py-20 md:py-28">
        <x-ui.heading :eyebrow="content('home.process_eyebrow', 'How we work')" :title="content_rich('home.process_title', 'A process built to de-risk the work.')">
            {!! content_rich('home.process_intro', 'Four phases, one continuous flow — each one de-risks the next.') !!}
        </x-ui.heading>

        <div class="mt-16 grid gap-y-12 lg:grid-cols-4 lg:gap-y-0" data-stagger>
            @foreach ($process as $i => $phase)
                <div class="group relative" data-stagger-item>
                    {{-- numbered node + connector to the next phase --}}
                    <div class="flex items-center">
                        <span class="relative z-10 flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-line bg-paper font-mono text-[0.78rem] font-bold transition-all duration-500 group-hover:border-ink group-hover:bg-ink group-hover:text-paper">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        @unless ($loop->last)
                            <span class="mx-3 hidden h-px grow bg-line lg:block"></span>
                            <x-ui.icon name="heroicon-m-chevron-right" class="hidden h-4 w-4 shrink-0 text-faint lg:block" />
                        @endunless
                    </div>

                    <div class="mt-7 lg:pr-10">
                        <h3 class="font-mono text-lg font-bold uppercase tracking-tight">{{ $phase->name }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-muted">{{ $phase->lead }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ──────────────────── Testimonials (auto carousel) ──────────────────── --}}
    @if ($testimonials->isNotEmpty())
        <section class="frame border-t border-line py-20 md:py-28">
            <div class="flex items-end justify-between gap-6">
                <x-ui.heading :eyebrow="content('home.signal_eyebrow', 'Signal')" :title="content_rich('home.signal_title', 'What partners say.')">
                    {!! content_rich('home.signal_intro', "Unfiltered words from the founders and teams we've embedded with.") !!}
                </x-ui.heading>

                @if ($testimonials->count() > 1)
                    <div class="hidden shrink-0 items-center gap-2.5 sm:flex">
                        <button type="button" class="t-prev inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors duration-300 hover:border-ink hover:bg-ink hover:text-paper" aria-label="Previous testimonial">
                            <x-ui.icon name="heroicon-m-arrow-left" class="h-4 w-4" />
                        </button>
                        <button type="button" class="t-next inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors duration-300 hover:border-ink hover:bg-ink hover:text-paper" aria-label="Next testimonial">
                            <x-ui.icon name="heroicon-m-arrow-right" class="h-4 w-4" />
                        </button>
                    </div>
                @endif
            </div>

            <div class="mt-14" data-reveal>
                <div class="swiper testimonials-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $t)
                            <div class="swiper-slide h-auto">
                                <figure class="flex h-full flex-col border border-line bg-paper p-8 md:p-9">
                                    <div class="font-mono text-5xl leading-none text-faint" aria-hidden="true">“</div>
                                    <blockquote class="mt-3 flex-1 text-[1.05rem] leading-relaxed text-ink">{!! rich_html($t->quote) !!}</blockquote>
                                    <figcaption class="mt-8 flex items-center gap-3 border-t border-line pt-6">
                                        @if ($t->avatar_url)
                                            <img src="{{ $t->avatar_url }}" alt="" loading="lazy" class="h-10 w-10 rounded-full grayscale">
                                        @endif
                                        <div class="font-mono text-xs uppercase tracking-wide">
                                            <div class="font-bold">{{ $t->author ?: $t->role }}</div>
                                            <div class="text-muted">{{ $t->author ? trim($t->role.($t->company ? ' · '.$t->company : '')) : $t->company }}</div>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="t-pagination mt-10 flex items-center justify-center gap-2"></div>
            </div>
        </section>
    @endif
</x-layouts.app>
