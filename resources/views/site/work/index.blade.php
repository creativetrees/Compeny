<x-layouts.app title="Work" description="Selected work from Creative Trees Group — design and engineering for products that scale.">
    {{-- ──────────────────── Header ──────────────────── --}}
    <section class="frame pt-32 pb-12 md:pt-40">
        <x-ui.eyebrow data-scramble>Selected work</x-ui.eyebrow>
        <h1 class="display mt-6 text-[2.6rem] leading-[0.98] sm:text-6xl md:text-7xl" data-reveal>
            Proof, not<br>promises.
        </h1>
        <p class="measure mt-7 text-[1rem] text-muted" data-reveal data-reveal-delay="0.1">
            A selection of products we've designed and engineered — for founders, teams, and the people who use what they ship.
        </p>
    </section>

    @php
        $counts = ['all' => $projects->count()];
        foreach ($categories as $c) {
            $counts[$c->slug] = $projects->filter(fn ($p) => $p->category?->slug === $c->slug)->count();
        }
    @endphp

    {{-- ──────────────────── Filter + grid ──────────────────── --}}
    <section class="frame border-t border-line py-12 md:py-16"
             x-data="{ cat: 'all', counts: {{ \Illuminate\Support\Js::from($counts) }} }">
        @if ($categories->isNotEmpty())
            <div class="mb-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2.5" role="group" aria-label="Filter work by category">
                    <button type="button" @click="cat = 'all'"
                            :aria-pressed="cat === 'all'"
                            :class="cat === 'all' ? 'border-ink bg-ink text-paper' : 'border-line text-muted hover:border-ink hover:text-ink'"
                            class="rounded-full border px-4 py-2 font-mono text-[0.7rem] uppercase tracking-widest transition-colors">
                        All
                    </button>
                    @foreach ($categories as $c)
                        <button type="button" @click="cat = '{{ $c->slug }}'"
                                :aria-pressed="cat === '{{ $c->slug }}'"
                                :class="cat === '{{ $c->slug }}' ? 'border-ink bg-ink text-paper' : 'border-line text-muted hover:border-ink hover:text-ink'"
                                class="rounded-full border px-4 py-2 font-mono text-[0.7rem] uppercase tracking-widest transition-colors">
                            {{ $c->name }}
                        </button>
                    @endforeach
                </div>

                <span class="label-mono shrink-0 text-faint"
                      x-text="counts[cat] + (counts[cat] === 1 ? ' project' : ' projects')">{{ $projects->count() }} {{ \Illuminate\Support\Str::plural('project', $projects->count()) }}</span>
            </div>
        @endif

        <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
            @forelse ($projects as $project)
                <div x-show="cat === 'all' || cat === '{{ $project->category?->slug }}'"
                     x-transition:enter="transition duration-500 ease-out"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <x-ui.project-card :project="$project" />
                </div>
            @empty
                <p class="col-span-full border border-line py-16 text-center font-mono text-sm uppercase tracking-widest text-faint">
                    Work is being published — check back soon.
                </p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
