@php
    $aboutBody = filled($settings->about_body)
        ? $settings->about_body
        : '<p>Creative Trees Group is a digital product studio. We design and engineer software the way the best in-house teams do — close to the problem, fast to ship, and obsessed with the details users actually feel.</p>';
    // about_heading is a RichEditor value but a column (not a page_content key, so
    // content_title() can't read it): strip block HTML to plain text for the headline.
    $aboutHeading = trim(html_entity_decode(strip_tags(preg_replace(
        ['#</(?:p|div|h[1-6]|li)>#i', '#<br\s*/?>#i'],
        "\n",
        filled($settings->about_heading) ? $settings->about_heading : 'A studio built like a product team.'
    )), ENT_QUOTES));
@endphp

<x-layouts.app title="About" :description="$settings->seo_description">
    {{-- Hero --}}
    <section class="frame relative pt-32 pb-16 md:pt-40 md:pb-20">
        <span class="tick left-6 top-28 md:left-10"></span>
        <span class="tick right-6 top-28 md:right-10"></span>

        <div class="max-w-4xl">
            <x-ui.eyebrow data-scramble>{{ content('about.hero_eyebrow', 'About') }}</x-ui.eyebrow>
            <h1 class="display mt-7 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]" data-reveal data-reveal-delay="0.08">
                {{ $aboutHeading }}
            </h1>
            <div class="measure mt-7 space-y-5 text-[1.05rem] leading-relaxed text-muted richtext" data-reveal data-reveal-delay="0.24">{!! $aboutBody !!}</div>
        </div>
    </section>

    {{-- Values --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <x-ui.heading :eyebrow="content('about.values_eyebrow', 'What we value')" :title="content_rich('about.values_title', 'How we think.')">
            {!! content_rich('about.values_intro', 'The handful of beliefs that shape how we design, build, and decide.') !!}
        </x-ui.heading>
        <div class="mt-14 grid border-l border-t border-line sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            @foreach ($values as $i => $value)
                <div class="group flex flex-col border-b border-r border-line p-8 transition-colors duration-500 hover:bg-panel md:p-9" data-stagger-item>
                    <div class="display text-[2.6rem] leading-none text-faint transition-colors duration-500 group-hover:text-ink">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3 class="mt-7 font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $value->title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $value->description }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Team --}}
    @if ($members->isNotEmpty())
        <section class="frame border-t border-line py-20 md:py-28">
            <div class="flex items-end justify-between gap-6">
                <x-ui.heading :eyebrow="content('about.team_eyebrow', 'The team')" :title="content_rich('about.team_title', 'Senior, embedded, accountable.')">
                    {!! content_rich('about.team_intro', 'Senior strategists, designers, and engineers who embed with your team and stay accountable end to end.') !!}
                </x-ui.heading>
                <a href="/team" class="link-underline hidden shrink-0 font-mono text-xs uppercase tracking-widest sm:inline-block">{{ content('about.team_link', 'Meet everyone') }} →</a>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
                @foreach ($members->take(4) as $member)
                    <a href="/team" class="group flex flex-col overflow-hidden border border-line bg-paper transition-all duration-500 hover:-translate-y-1.5 hover:border-ink/30 hover:shadow-[0_22px_55px_-32px_rgba(10,10,10,0.4)]" data-stagger-item>
                        <div class="relative aspect-[4/5] overflow-hidden bg-panel">
                            @if ($member->photo_url)
                                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="lazy" class="h-full w-full object-cover grayscale transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04] group-hover:grayscale-0">
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-mono text-sm font-bold uppercase tracking-tight">{{ $member->name }}</h3>
                            <p class="label-mono mt-1.5 text-muted">{{ $member->role }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Clients --}}
    @if ($clients->isNotEmpty())
        <section class="frame border-t border-line py-14">
            <div class="mb-4 flex justify-center">
                <x-ui.eyebrow plain>▪ {{ content('about.clients_eyebrow', 'In good company') }}</x-ui.eyebrow>
            </div>
            <p class="mx-auto mb-10 max-w-xl text-center text-sm text-muted">{!! content_rich('about.clients_intro', "A few of the teams we've designed and built alongside.") !!}</p>
            <x-ui.marquee :clients="$clients" />
        </section>
    @endif
</x-layouts.app>
