@php
    $count = str_pad(count($phases), 2, '0', STR_PAD_LEFT);
@endphp

<x-layouts.app
    :title="'Process'"
    :description="'How Creative Trees Group works — a four-phase process built to de-risk the work: Discover, Design, Build, and Scale.'"
>
    {{-- ──────────────────── Page header ──────────────────── --}}
    <section class="frame pt-32 pb-16 md:pt-40 md:pb-20">
        <span class="tick left-6 top-32 md:left-10"></span>
        <span class="tick right-6 top-32 md:right-10"></span>

        <div class="max-w-4xl">
            <x-ui.eyebrow data-scramble>{{ content('process.hero_eyebrow', 'How we work') }}</x-ui.eyebrow>

            <h1 class="display mt-7 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]" data-reveal data-reveal-delay="0.08">
                {{ content_title('process.hero_title', 'A process built to de-risk the work.') }}
            </h1>

            <p class="measure mt-7 text-[1.02rem] text-muted" data-reveal data-reveal-delay="0.24">
                {!! content_rich('process.hero_intro', 'Four phases, one embedded team, zero handoffs. We spend the riskiest assumptions first and ship working software every week — so the path from idea to scale is something you can see, not something you have to trust.') !!}
            </p>
        </div>
    </section>

    {{-- ──────────────────── Phases (numbered sequence) ──────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <div class="flex items-center justify-between">
            <x-ui.eyebrow plain>▪ {{ content('process.sequence_eyebrow', 'The sequence') }}</x-ui.eyebrow>
            <span class="label-mono text-faint">{{ $count }} {{ content('process.phases_label', 'phases') }}</span>
        </div>

        <div class="richtext measure mt-6 text-[1rem] text-muted">{!! content_rich('process.sequence_intro', 'Four phases in one continuous flow — each closing the riskiest gaps before the next begins.') !!}</div>

        <div class="mt-12 grid border-l border-t border-line sm:grid-cols-2 md:mt-14" data-stagger>
            @forelse ($phases as $i => $phase)
                <div class="group flex flex-col border-b border-r border-line p-8 transition-colors duration-500 hover:bg-panel md:p-10" data-stagger-item>
                    <div class="display text-[2.6rem] leading-none text-faint transition-colors duration-500 group-hover:text-ink">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h2 class="display mt-6 text-2xl leading-none sm:text-[1.7rem]">{{ $phase->name }}</h2>
                    <p class="mt-4 text-[0.97rem] font-medium leading-snug text-ink">{{ $phase->lead }}</p>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{!! rich_html($phase->body) !!}</p>

                    <div class="mt-auto border-t border-line pt-6">
                        <div class="label-mono mb-3 text-faint">{{ content('process.deliverables_label', 'Deliverables') }}</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($phase->deliverables ?? [] as $deliverable)
                                <span class="border border-line px-2.5 py-1 font-mono text-[0.62rem] uppercase tracking-wide text-muted transition-colors duration-300 group-hover:border-ink/25 group-hover:text-ink">{{ $deliverable }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full border-b border-r border-line p-8 text-center font-mono text-sm uppercase tracking-wide text-faint">{!! content_rich('process.phases_empty', 'The process is being documented — check back soon.') !!}</p>
            @endforelse
        </div>
    </section>

    {{-- ──────────────────── Operating principles (light) ──────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <x-ui.heading :eyebrow="content('process.principles_eyebrow', 'Operating principles')" :title="content_rich('process.principles_title', 'The rules that keep the work honest.')">
            {!! content_rich('process.principles_intro', 'Four constraints we hold on every engagement — the reason the process stays honest when the deadlines get loud.') !!}
        </x-ui.heading>

        <div class="mt-14 grid border-l border-t border-line sm:grid-cols-2 lg:grid-cols-4" data-stagger>
            @foreach ($principles as $i => $principle)
                <div class="group flex flex-col border-b border-r border-line p-8 transition-colors duration-500 hover:bg-panel md:p-9" data-stagger-item>
                    <div class="display text-[2.6rem] leading-none text-faint transition-colors duration-500 group-hover:text-ink">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <h3 class="mt-7 font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $principle->title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{!! rich_html($principle->description) !!}</p>
                </div>
            @endforeach
        </div>
    </section>

</x-layouts.app>
