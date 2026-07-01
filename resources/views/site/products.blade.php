<x-layouts.app
    :title="'Products'"
    :description="'Productized starters, templates, and services from Creative Trees Group — built to ship in days, not months.'">

    {{-- ───────────────────────── Page header ───────────────────────── --}}
    <section class="frame relative pt-32 pb-16 md:pt-40 md:pb-24">
        <span class="tick left-6 top-28 md:left-10"></span>
        <span class="tick right-6 top-28 md:right-10"></span>

        <div class="max-w-4xl">
            <x-ui.eyebrow data-scramble>{{ content('products.hero_eyebrow', 'Products') }}</x-ui.eyebrow>

            <h1 class="display mt-7 text-[2.5rem] leading-[0.98] sm:text-6xl md:text-[4.6rem]">
                <span class="block" data-reveal data-reveal-delay="0.08">{!! content_rich('products.hero_line1', 'Starters that ship') !!}</span>
                <span class="block" data-reveal data-reveal-delay="0.16">{!! content_rich('products.hero_line2', 'in days, not months.') !!}</span>
            </h1>

            <p class="measure mt-7 text-[1rem] text-muted" data-reveal data-reveal-delay="0.3">
                {!! content_rich('products.hero_intro', "Productized building blocks — SaaS foundations, design-system templates, and embedded services — each engineered to the same standard as our custom work. Pick a starting point, tell us where you're headed, and we tailor it to your roadmap.") !!}
            </p>
        </div>
    </section>

    {{-- ───────────────────────── Catalog grid ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        @if ($products->isNotEmpty())
            <div class="mb-12 flex items-end justify-between gap-6">
                <x-ui.eyebrow plain>▪ {{ $products->count() }} {{ \Illuminate\Support\Str::plural('product', $products->count()) }} available</x-ui.eyebrow>
                <span class="label-mono hidden text-faint sm:inline-block">{{ content('products.leadtime_label', 'Lead-time · 1–3 weeks') }}</span>
            </div>

            <div class="grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3" data-stagger>
                @foreach ($products as $product)
                    <article class="group flex flex-col" data-stagger-item>
                        {{-- Cover --}}
                        <div class="relative aspect-[16/10] overflow-hidden border border-line bg-panel">
                            @if ($product->cover_url)
                                <x-ui.img :src="$product->cover_url" :alt="$product->title"
                                     sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                     class="h-full w-full object-cover grayscale transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:grayscale-0 group-hover:scale-[1.03]" />
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <span class="font-mono text-[0.62rem] uppercase tracking-[0.3em] text-faint">
                                        {{ $product->type }}
                                    </span>
                                </div>
                            @endif
                            <span class="tick right-3 top-3"></span>
                        </div>

                        {{-- Type + index --}}
                        <div class="mt-5 flex items-center justify-between gap-4">
                            <span class="label-mono">{{ $product->type }}</span>
                            <span class="label-mono text-faint">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        {{-- Title --}}
                        <h2 class="mt-3 font-mono text-lg font-bold uppercase leading-tight tracking-tight">
                            {{ $product->title }}
                        </h2>

                        {{-- Summary --}}
                        <p class="mt-2.5 text-sm leading-relaxed text-muted">{!! rich_html($product->summary) !!}</p>

                        {{-- Features --}}
                        @if (! empty($product->features))
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($product->features as $feature)
                                    <span class="border border-line px-2.5 py-1 font-mono text-[0.64rem] uppercase tracking-wide text-ink/70">{{ $feature }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Price + CTA (pinned to bottom for grid alignment) --}}
                        <div class="mt-auto pt-7">
                            <div class="rule"></div>
                            <div class="mt-6 flex flex-wrap items-end justify-between gap-4">
                                <div>
                                    <div class="label-mono text-faint">{{ content('products.investment_label', 'Investment') }}</div>
                                    <div class="mt-1.5 font-mono text-xl font-bold tracking-tight">{{ $product->price_label }}</div>
                                </div>
                                <x-ui.button :href="$product->cta_url ?? '/start'" variant="ghost" :magnetic="false">
                                    {{ $product->cta_label ?? 'Request access' }}
                                </x-ui.button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Empty state --}}
            <div class="relative border border-line bg-panel px-6 py-24 text-center md:py-28">
                <span class="tick left-3 top-3"></span>
                <span class="tick right-3 top-3"></span>
                <span class="tick bottom-3 left-3"></span>
                <span class="tick bottom-3 right-3"></span>

                <div class="measure mx-auto">
                    <div class="flex justify-center">
                        <x-ui.eyebrow plain>▪ {{ content('products.empty_eyebrow', 'Catalog in progress') }}</x-ui.eyebrow>
                    </div>
                    <p class="mt-5 text-sm leading-relaxed text-muted">
                        {!! content_rich('products.empty_message', "We're packaging our next set of starters. Tell us what you're building and we'll scope a custom path in the meantime.") !!}
                    </p>
                    <div class="mt-8 flex justify-center">
                        <x-ui.button href="/start">Start a project</x-ui.button>
                    </div>
                </div>
            </div>
        @endif
    </section>

</x-layouts.app>
