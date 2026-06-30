<x-layouts.app
    :title="'Team'"
    :description="'Creative Trees is a small, senior team of strategists, designers, and engineers who embed directly with yours.'">

    {{-- ───────────────────────── Page header ───────────────────────── --}}
    <section class="frame relative pt-36 pb-16 md:pt-44 md:pb-24">
        <span class="tick left-6 top-28 md:left-10"></span>
        <span class="tick right-6 top-28 md:right-10"></span>

        <div class="max-w-4xl">
            <x-ui.eyebrow data-scramble>{{ content('team.hero_eyebrow', 'Team') }}</x-ui.eyebrow>

            <h1 class="display mt-7 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]" data-reveal data-reveal-delay="0.08">
                {!! nl2br(e(content('team.hero_title', "The people behind\nthe work."))) !!}
            </h1>

            <p class="measure mt-7 text-[1rem] text-muted" data-reveal data-reveal-delay="0.3">
                {{ content('team.hero_intro', 'No account layers, no handoffs. Creative Trees is a small, senior team of strategists, designers, and engineers who embed directly with yours — and stay accountable from the first sketch to production traffic.') }}
            </p>
        </div>
    </section>

    {{-- ───────────────────────── Member grid ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <div class="flex items-end justify-between gap-6">
            <x-ui.eyebrow plain>▪ {{ content('team.studio_eyebrow', 'The studio') }}</x-ui.eyebrow>
            <span class="label-mono shrink-0">{{ str_pad($members->count(), 2, '0', STR_PAD_LEFT) }} / People</span>
        </div>

        <p class="measure mt-6 text-[1rem] text-muted">{{ content('team.studio_intro', "The senior people who'll actually do your work — no account layers, no handoffs.") }}</p>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
            @forelse ($members as $member)
                @php
                    $initials = collect(preg_split('/\s+/', trim($member->name)))
                        ->filter()
                        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
                        ->take(2)
                        ->implode('');
                @endphp

                <article class="group flex flex-col overflow-hidden border border-line bg-paper transition-all duration-500 hover:-translate-y-1.5 hover:border-ink/30 hover:shadow-[0_22px_55px_-32px_rgba(10,10,10,0.4)]" data-stagger-item>
                    {{-- Portrait --}}
                    <div class="relative aspect-[4/5] overflow-hidden bg-panel">
                        @if ($member->photo_url)
                            <x-ui.img :src="$member->photo_url" :alt="$member->name"
                                 sizes="(min-width: 768px) 33vw, 50vw"
                                 class="h-full w-full object-cover grayscale transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04] group-hover:grayscale-0" />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <span class="display text-6xl text-faint">{{ $initials }}</span>
                            </div>
                        @endif
                        <span class="absolute left-3 top-3 bg-paper px-2 py-1 font-mono text-[0.58rem] uppercase tracking-widest text-ink">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    {{-- Identity --}}
                    <div class="flex flex-1 flex-col p-6 md:p-7">
                        <h3 class="font-mono text-[1.05rem] font-bold uppercase tracking-tight">{{ $member->name }}</h3>
                        <div class="label-mono mt-2 text-muted">{{ $member->role }}</div>

                        @if ($member->bio)
                            <p class="mt-4 text-sm leading-relaxed text-muted">{{ $member->bio }}</p>
                        @endif

                        @if (! empty($member->socials))
                            <div class="mt-auto flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-line pt-5">
                                @foreach ($member->socials as $platform => $url)
                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                           class="link-underline font-mono text-[0.64rem] uppercase tracking-widest text-ink/75 hover:text-ink">{{ $platform }} ↗</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full flex flex-col items-center border border-line bg-panel px-6 py-20 text-center">
                    <span class="label-mono text-faint">00 / Roster</span>
                    <p class="measure mt-4 text-[1rem] text-muted">
                        {{ content('team.empty_message', 'The studio roster is being assembled. In the meantime, the work speaks for itself.') }}
                    </p>
                    <div class="mt-7">
                        <x-ui.button href="/work" variant="ghost" :magnetic="false">View work</x-ui.button>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

</x-layouts.app>
