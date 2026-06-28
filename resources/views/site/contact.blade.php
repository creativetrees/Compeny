@php
    $brand = $settings->brand_name ?? 'Creative Trees Group';
    $email = $settings->contact_email ?? 'hello@creativetrees.group';
    $phone = $settings->contact_phone ?? null;
    $address = $settings->contact_address ?? null;
    $socials = $settings->social_links ?? [];
    $telHref = $phone ? 'tel:' . preg_replace('/[^+0-9]/', '', $phone) : null;
@endphp

<x-layouts.app
    :title="'Contact'"
    :description="'Tell us what you\'re building. We\'ll reply with scope, timeline, and the shortest honest path to a product that ships.'"
>
    {{-- ───────────────────────── Page header ───────────────────────── --}}
    <section class="frame relative pt-32 pb-20 md:pt-40 md:pb-28">
        <span class="tick left-6 top-32 md:left-10 md:top-40"></span>
        <span class="tick right-6 top-32 md:right-10 md:top-40"></span>

        <div class="grid gap-x-8 gap-y-12 md:grid-cols-12 md:items-end">
            {{-- Statement --}}
            <div class="md:col-span-8">
                <x-ui.eyebrow data-scramble>{{ content('contact.hero_eyebrow', 'Contact') }} ›</x-ui.eyebrow>

                <h1 class="display mt-7 text-[3rem] leading-[0.92] sm:text-7xl md:text-[7.5rem]" data-reveal data-reveal-delay="0.1">
                    {{ content('contact.hero_title', "Let's talk.") }}
                </h1>

                <p class="measure mt-7 text-[1rem] text-muted" data-reveal data-reveal-delay="0.28">
                    {{ content('contact.hero_intro', "A fully-scoped build or a half-formed idea — either is a good place to start. Tell us where you're headed and we'll come back with the shortest honest path to get there.") }}
                </p>
            </div>

            {{-- Meta — fills the full-width right column, keeps the band balanced --}}
            <dl class="grid gap-7 md:col-span-3 md:col-start-10" data-reveal data-reveal-delay="0.4">
                <div>
                    <dt class="label-mono text-faint">{{ content('contact.meta_response_label', 'Response') }}</dt>
                    <dd class="mt-2 font-mono text-sm text-ink">{{ content('contact.meta_response_value', 'Within 1 business day') }}</dd>
                </div>
                <div>
                    <dt class="label-mono text-faint">{{ content('contact.meta_based_label', 'Based') }}</dt>
                    <dd class="mt-2 font-mono text-sm text-ink">{{ content('contact.meta_based_value', 'Jakarta · Remote-first') }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- ───────────────────────── Inquiry + details ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-12">

            {{-- Left: route to the project brief --}}
            <div class="md:col-span-6" data-reveal>
                <p class="label-mono text-faint">01 — {{ content('contact.inquiry_label', 'New project') }}</p>

                <p class="display mt-7 text-[1.7rem] leading-[1.06] sm:text-3xl md:text-[2.5rem]">
                    {{ content('contact.inquiry_title', 'Project inquiries move fastest through the brief.') }}
                </p>

                <p class="measure mt-6 text-[0.97rem] leading-relaxed text-muted">
                    {{ content('contact.inquiry_body', 'Answer a handful of questions about scope, timeline, and budget. We read every brief ourselves and reply within one business day — with a real next step, never an auto-response.') }}
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <x-ui.button href="/start">{{ content('contact.inquiry_cta', 'Start a project') }}</x-ui.button>
                    <x-ui.button href="/work" variant="ghost" :magnetic="false">{{ content('contact.inquiry_secondary_cta', 'See the work') }}</x-ui.button>
                </div>
            </div>

            {{-- Right: direct lines --}}
            <div class="md:col-span-5 md:col-start-8" data-reveal data-reveal-delay="0.1">
                <p class="label-mono text-faint">02 — {{ content('contact.direct_label', 'Direct lines') }}</p>

                <dl class="mt-7 border-t border-line" data-stagger>
                    <div class="flex flex-col gap-2 border-b border-line py-6" data-stagger-item>
                        <dt class="label-mono text-faint">{{ content('contact.email_label', 'Email') }}</dt>
                        <dd>
                            <a href="mailto:{{ $email }}" class="link-underline text-lg">{{ $email }}</a>
                        </dd>
                    </div>

                    @if ($phone)
                        <div class="flex flex-col gap-2 border-b border-line py-6" data-stagger-item>
                            <dt class="label-mono text-faint">{{ content('contact.phone_label', 'Phone') }}</dt>
                            <dd>
                                <a href="{{ $telHref }}" class="link-underline text-lg">{{ $phone }}</a>
                            </dd>
                        </div>
                    @endif

                    @if ($address)
                        <div class="flex flex-col gap-2 border-b border-line py-6" data-stagger-item>
                            <dt class="label-mono text-faint">{{ content('contact.studio_label', 'Studio') }}</dt>
                            <dd class="max-w-xs text-[0.97rem] leading-relaxed text-muted">{{ $address }}</dd>
                        </div>
                    @endif

                    @if (! empty($socials))
                        <div class="flex flex-col gap-3 border-b border-line py-6" data-stagger-item>
                            <dt class="label-mono text-faint">{{ content('contact.elsewhere_label', 'Elsewhere') }}</dt>
                            <dd class="flex flex-wrap gap-x-5 gap-y-2">
                                @foreach ($socials as $key => $social)
                                    @php
                                        // Supports both new [{platform,url}] and legacy {platform: url}.
                                        $sUrl = is_array($social) ? ($social['url'] ?? null) : $social;
                                        $sName = is_array($social) ? ($social['platform'] ?? null) : $key;
                                    @endphp
                                    @if ($sUrl && $sName)
                                        <a href="{{ $sUrl }}" target="_blank" rel="noopener"
                                           class="link-underline font-mono text-xs uppercase tracking-wide text-ink/80 hover:text-ink">{{ $sName }}</a>
                                    @endif
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>

                <p class="mt-6 text-sm leading-relaxed text-muted">
                    {{ content('contact.note', 'Press, partnerships, or careers? The same inbox reaches us — just say which.') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ───────────────────────── Oversized mailto ───────────────────────── --}}
    <section class="frame border-t border-line py-20 md:py-28">
        <x-ui.eyebrow class="mb-8" data-scramble>{{ content('contact.write_eyebrow', 'Or just write') }}</x-ui.eyebrow>

        <a href="mailto:{{ $email }}"
           class="link-underline display block break-words text-[8.5vw] leading-[0.9] md:text-[6vw]"
           data-reveal>{{ $email }}</a>

        <p class="label-mono measure mt-10 text-faint" data-reveal data-reveal-delay="0.12">
            {{ content('contact.write_note', 'Still, the fastest path is the brief — it gets you a scoped answer, not a thread.') }}
        </p>
    </section>
</x-layouts.app>
