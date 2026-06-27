@props(['project'])

<a href="/work/{{ $project->slug }}" class="group block" data-stagger-item data-cursor-grow>
    <div class="relative aspect-[16/10] overflow-hidden border border-line bg-panel">
        @if ($project->cover_url)
            <img src="{{ $project->cover_url }}" alt="{{ $project->title }}" loading="lazy"
                 class="h-full w-full object-cover grayscale transition-all duration-[900ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.03] group-hover:grayscale-0">
        @endif

        {{-- subtle veil + corner registration tick --}}
        <div class="absolute inset-0 bg-ink/0 transition-colors duration-500 group-hover:bg-ink/10"></div>
        <span class="tick right-3 top-3"></span>

        {{-- readable hover label (paper pill, works over any image) --}}
        <div class="absolute bottom-3 left-3 translate-y-2 opacity-0 transition-all duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-y-0 group-hover:opacity-100">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-paper px-3 py-1.5 font-mono text-[0.6rem] uppercase tracking-widest text-ink">View case →</span>
        </div>
    </div>

    <div class="mt-4 flex items-baseline justify-between gap-4">
        <h3 class="font-mono text-sm font-bold uppercase tracking-tight">{{ $project->title }}</h3>
        <span class="label-mono shrink-0">{{ $project->year }}</span>
    </div>
    <p class="mt-1.5 text-[0.92rem] leading-relaxed text-muted">
        @if ($project->client_name)<span class="text-ink">{{ $project->client_name }}</span> — @endif{{ \Illuminate\Support\Str::limit($project->summary ?? '', 68) }}
    </p>
</a>
