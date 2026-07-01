@php
    $brand = $settings->brand_name ?? 'Creative Trees Group';
    $email = $settings->contact_email ?? 'hello@creativetrees.group';
    $phone = $settings->contact_phone;
    $address = $settings->contact_address;
    $socials = $settings->social_links ?? [];
    // Plain-text tagline for the baseline: turn block boundaries into spaces so
    // multi-paragraph rich HTML doesn't smash words together ("Built tocompound").
    $taglinePlain = trim(preg_replace('/\s+/', ' ', strip_tags(
        str_replace(['</p>', '<br>', '<br/>', '<br />'], ' ', (string) $settings->footer_tagline)
    )));
    $cols = [
        'Studio' => \App\Models\NavLink::query()->where('location', 'footer_studio')->ordered()->get(),
        'Company' => \App\Models\NavLink::query()->where('location', 'footer_company')->ordered()->get(),
    ];
@endphp

<footer class="surface-dark relative overflow-hidden">
    {{-- ── CTA band ── --}}
    <section class="frame !border-0 py-20 md:py-28">
        <div class="grid gap-y-10 md:grid-cols-12 md:items-end md:gap-x-10">
            <div class="md:col-span-7" data-reveal>
                <x-ui.eyebrow class="mb-7">{{ $settings->footer_cta_eyebrow ?: "Let's build" }}</x-ui.eyebrow>
                <h2 class="display text-[2.6rem] leading-[0.95] sm:text-5xl md:text-[4.2rem]">
                    {!! nl2br(e(strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", (string) ($settings->footer_cta_title ?: "Have something\nworth building?"))))) !!}
                </h2>
            </div>
            <div class="flex flex-col items-start md:col-span-5 md:items-end md:text-right" data-reveal data-reveal-delay="0.1">
                <div class="mb-7 max-w-sm text-[1rem] leading-relaxed text-[#9a9a96] [&_a]:text-paper [&_a]:underline [&_p]:m-0 [&_p+p]:mt-3 [&_strong]:text-paper">
                    {!! rich_html($settings->footer_cta_body ?: "Tell us where you're headed. We'll tell you the shortest honest path to get there.") !!}
                </div>
                <x-ui.button href="{{ $settings->footer_cta_url ?: '/start' }}" variant="invert">{{ $settings->footer_cta_label ?: 'Start a project' }}</x-ui.button>
            </div>
        </div>
    </section>

    {{-- ── Link grid ── --}}
    <section class="frame !border-0 border-t border-[#1c1c1c] py-16">
        <div class="grid gap-x-10 gap-y-12 md:grid-cols-12">
            {{-- brand --}}
            <div class="md:col-span-4">
                <a href="/" class="group inline-flex items-center gap-2.5">
                    @if ($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="{{ $settings->brand_name ?? 'Creative Trees Group' }}" class="h-8 w-auto">
                    @else
                        <x-ui.logo-mark class="h-7 w-7 text-paper transition-transform duration-500 group-hover:rotate-90" />
                    @endif
                    @if (filled($settings->logo_text ?: $settings->brand_name))
                        <span class="font-mono text-[0.95rem] font-bold uppercase tracking-tight">{{ $settings->logo_text ?: ($settings->brand_name ?? 'Creative Trees Group') }}</span>
                    @endif
                </a>
                <div class="mt-5 max-w-xs text-sm leading-relaxed text-[#9a9a96] richtext [&_a]:text-paper [&_strong]:text-paper">
                    {!! rich_html(filled($settings->footer_tagline) ? $settings->footer_tagline : 'Designed and built to compound.') !!}
                </div>
                <a href="mailto:{{ $email }}" class="link-underline mt-7 inline-block font-mono text-sm text-paper">{{ $email }}</a>
            </div>

            {{-- evenly-spaced columns --}}
            <div class="grid grid-cols-2 gap-x-8 gap-y-10 sm:grid-cols-3 md:col-span-8">
                @foreach ($cols as $title => $links)
                    <nav aria-label="{{ $title }}">
                        <p class="label-mono mb-5 text-[#8a8a86]">{{ $title }}</p>
                        <ul class="space-y-3">
                            @foreach ($links as $link)
                                <li><a href="{{ $link->url }}" class="link-underline text-sm text-[#cfcfcc] transition-colors hover:text-paper">{{ $link->label }}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach

                <div class="col-span-2 sm:col-span-1">
                    <p class="label-mono mb-5 text-[#8a8a86]">{{ content('footer.contact_label', 'Contact') }}</p>
                    <ul class="space-y-3 text-sm text-[#cfcfcc]">
                        @if ($phone)<li>{{ $phone }}</li>@endif
                        @if ($address)<li class="text-[#9a9a96]">{{ $address }}</li>@endif
                    </ul>
                    @if (! empty($socials))
                        <div class="mt-6 flex flex-wrap gap-x-4 gap-y-2.5">
                            @foreach ($socials as $key => $social)
                                @php
                                    // Supports both new [{platform,url}] and legacy {platform: url}.
                                    $sUrl = is_array($social) ? ($social['url'] ?? null) : $social;
                                    $sName = is_array($social) ? ($social['platform'] ?? null) : $key;
                                @endphp
                                @if ($sUrl && $sName)
                                    <a href="{{ $sUrl }}" target="_blank" rel="noopener"
                                       class="link-underline font-mono text-xs uppercase tracking-wide text-[#9a9a96] hover:text-paper">{{ $sName }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ── Binary wordmark — letters built from flowing 0/1. Enlarges + wraps on
         mobile so the binary stays legible; one clean line on tablet/desktop. ── --}}
    <div class="frame !border-0 overflow-hidden pb-8 pt-8" aria-hidden="true">
        <div class="binary-text display select-none text-center text-[13.5vw] leading-[1.05] sm:whitespace-nowrap sm:text-[7.6vw] sm:leading-[0.9] lg:text-[6.8vw] lg:leading-[0.85]"
             data-binary-text>{{ \Illuminate\Support\Str::upper($settings->footer_watermark ?: $brand) }}</div>
    </div>

    {{-- ── Baseline ── --}}
    <div class="frame !border-0 flex flex-col gap-2 border-t border-[#1c1c1c] py-6 font-mono text-[0.72rem] uppercase tracking-wide text-[#8a8a86] sm:flex-row sm:items-center sm:justify-between">
        <span>© {{ date('Y') }} {{ $settings->footer_copyright ?: $brand }}</span>
        <span>{{ ($settings->footer_location ?: 'Jakarta · Remote-first').' — '.($taglinePlain ?: 'Built to compound') }}</span>
    </div>
</footer>
