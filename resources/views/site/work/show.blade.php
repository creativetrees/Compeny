<x-layouts.app :title="$project->title" :description="$project->summary">
    {{-- ──────────────────── Header ──────────────────── --}}
    <section class="frame pt-32 pb-12 md:pt-40 md:pb-16">
        <a href="/work" class="link-underline font-mono text-xs uppercase tracking-widest text-muted">← Work</a>

        <h1 class="display mt-10 max-w-5xl text-[2.5rem] leading-[0.95] sm:text-6xl md:mt-12 md:text-[4.75rem]" data-reveal>
            {{ $project->title }}
        </h1>

        <div class="mt-10 grid gap-x-8 gap-y-10 border-t border-line pt-10 md:mt-14 md:grid-cols-12" data-reveal data-reveal-delay="0.08">
            <p class="measure text-[1.05rem] leading-relaxed text-muted md:col-span-7">{{ $project->summary }}</p>

            <dl class="flex flex-wrap gap-x-12 gap-y-6 md:col-span-4 md:col-start-9">
                @if ($project->client_name)
                    <div>
                        <dt class="label-mono text-faint">Client</dt>
                        <dd class="mt-2 font-mono text-sm font-medium uppercase tracking-tight text-ink">{{ $project->client_name }}</dd>
                    </div>
                @endif
                @if ($project->year)
                    <div>
                        <dt class="label-mono text-faint">Year</dt>
                        <dd class="mt-2 font-mono text-sm font-medium uppercase tracking-tight text-ink">{{ $project->year }}</dd>
                    </div>
                @endif
                @if ($project->role)
                    <div>
                        <dt class="label-mono text-faint">Role</dt>
                        <dd class="mt-2 font-mono text-sm font-medium uppercase tracking-tight text-ink">{{ $project->role }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </section>

    {{-- ──────────────────── Cover ──────────────────── --}}
    @if ($project->cover_url)
        <section class="frame pb-8 md:pb-12">
            <div class="relative aspect-[16/9] overflow-hidden border border-line bg-panel" data-reveal>
                <x-ui.img :src="$project->cover_url" :alt="$project->title" fetchpriority="high" loading="eager" sizes="100vw" class="h-full w-full object-cover" data-parallax="0.08" />
                <span class="tick left-3 top-3"></span>
                <span class="tick right-3 bottom-3"></span>
            </div>
        </section>
    @endif

    {{-- ──────────────────── Body + aside ──────────────────── --}}
    @if ($project->body || ! empty($project->services) || $project->website_url)
        <section class="frame border-t border-line py-20 md:py-28">
            <div class="grid gap-x-10 gap-y-14 md:grid-cols-12">
                @if ($project->body)
                    <div class="md:col-span-7">
                        <x-ui.eyebrow plain class="mb-7">Overview</x-ui.eyebrow>
                        <div class="measure whitespace-pre-line text-[1.05rem] leading-relaxed text-ink/90" data-reveal>{{ $project->body }}</div>
                    </div>
                @endif

                @if (! empty($project->services) || $project->website_url)
                    <aside class="md:col-span-4 md:col-start-9 md:self-start md:border-l md:border-line md:pl-10">
                        @if (! empty($project->services))
                            <p class="label-mono mb-4">Services</p>
                            <div class="mb-10 flex flex-wrap gap-2">
                                @foreach ($project->services as $s)
                                    <span class="border border-line px-2.5 py-1 font-mono text-[0.64rem] uppercase tracking-wide text-ink/70">{{ $s }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($project->website_url)
                            <x-ui.button :href="$project->website_url" variant="ghost" :magnetic="false">Visit site</x-ui.button>
                        @endif
                    </aside>
                @endif
            </div>
        </section>
    @endif

    {{-- ──────────────────── Gallery ──────────────────── --}}
    @if (! empty($project->gallery))
        <section class="frame border-t border-line py-20 md:py-28">
            <div class="mb-10 flex items-end justify-between gap-6">
                <x-ui.eyebrow plain>Gallery</x-ui.eyebrow>
                <span class="label-mono text-faint">{{ str_pad(count($project->gallery), 2, '0', STR_PAD_LEFT) }} frames</span>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 md:gap-8" data-stagger>
                @foreach ($project->gallery as $img)
                    <div class="aspect-[4/3] overflow-hidden border border-line bg-panel" data-stagger-item>
                        <img src="{{ $img }}" alt="" loading="lazy" class="h-full w-full object-cover grayscale transition-all duration-700 hover:grayscale-0">
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ──────────────────── More work ──────────────────── --}}
    @if ($more->isNotEmpty())
        <section class="frame border-t border-line py-20 md:py-28">
            <div class="mb-10 flex items-end justify-between gap-6">
                <x-ui.heading eyebrow="Keep looking" title="More work." />
                <a href="/work" class="link-underline hidden shrink-0 font-mono text-xs uppercase tracking-widest sm:inline-block">All work →</a>
            </div>
            <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2" data-stagger>
                @foreach ($more as $project)
                    <x-ui.project-card :project="$project" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
