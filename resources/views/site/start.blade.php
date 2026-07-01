<x-layouts.app title="Start a project" description="Tell us where you're headed and we'll map the shortest honest path to get there.">
    <section class="frame border-t border-transparent pt-32 pb-16 md:pt-40">
        <div class="grid gap-14 md:grid-cols-12 md:gap-10">
            {{-- Left: framing --}}
            <div class="md:col-span-5">
                <x-ui.eyebrow data-scramble>{{ content('start.hero_eyebrow', 'Start a project') }}</x-ui.eyebrow>
                <h1 class="display mt-6 text-[2.4rem] leading-[0.98] sm:text-5xl" data-reveal>
                    {!! nl2br(e(content_title('start.hero_title', "Tell us where\nyou're headed."))) !!}
                </h1>
                <p class="mt-6 max-w-sm text-[0.97rem] text-muted" data-reveal data-reveal-delay="0.1">
                    {!! content_rich('start.hero_intro', "Share a few details about what you're building. We'll tell you the shortest honest path to get there.") !!}
                </p>

                <div class="mt-12 space-y-px border-t border-line" data-stagger>
                    @foreach ($steps as $i => $step)
                        <div class="flex gap-5 border-b border-line py-5" data-stagger-item>
                            <span class="label-mono mt-0.5 text-faint">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="font-mono text-sm font-bold uppercase tracking-tight">{{ $step->title }}</h3>
                                <p class="mt-1 text-sm text-muted">{!! rich_html($step->description) !!}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: form / success --}}
            <div class="md:col-span-7 md:pl-8">
                @if (session('lead_sent'))
                    <div class="flex h-full flex-col justify-center border border-line bg-panel p-10 text-center" data-reveal>
                        <div class="mx-auto mb-6 flex h-12 w-12 items-center justify-center rounded-full bg-ink text-paper">✓</div>
                        <h2 class="display text-2xl">{{ content('start.success_title', 'Brief received.') }}</h2>
                        <p class="mx-auto mt-4 max-w-sm text-[0.97rem] text-muted">
                            {{ content('start.success_message', 'Thank you — your brief is in. A real person will read it and reply within one business day.') }}
                        </p>
                        <div class="mt-8 flex justify-center">
                            <x-ui.button href="/work" variant="ghost" :magnetic="false">Browse our work</x-ui.button>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('leads.store') }}" class="space-y-8" data-reveal>
                        @csrf

                        @if ($errors->any())
                            <div role="alert" tabindex="-1" data-error-summary
                                 class="border-2 border-ink bg-panel px-5 py-4 font-mono text-sm text-ink">
                                <p class="font-bold uppercase tracking-wide">Please fix {{ $errors->count() }} {{ \Illuminate\Support\Str::plural('field', $errors->count()) }}:</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-muted">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Honeypot: invisible to humans; bots that fill it are silently dropped. --}}
                        <div class="hidden" aria-hidden="true">
                            <label>Company URL
                                <input type="text" name="company_url" tabindex="-1" autocomplete="off">
                            </label>
                        </div>

                        <div class="grid gap-8 sm:grid-cols-2">
                            <x-ui.field name="name" label="Name" required />
                            <x-ui.field name="email" label="Email" type="email" required />
                            <x-ui.field name="company" label="Company" />
                            <x-ui.field name="phone" label="Phone" type="tel" />
                        </div>

                        <div class="grid gap-8 sm:grid-cols-2">
                            <x-ui.field name="budget" label="Budget" type="select" :options="$budgets" placeholder="Select a range" />
                            <x-ui.field name="service_interest" label="What you need" type="select"
                                        :options="$services->pluck('title')->all()" placeholder="Select a service" />
                        </div>

                        <x-ui.field name="message" label="About the project" type="textarea" required
                                    placeholder="What are you building, and what does success look like?" />

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" data-magnetic class="btn">{{ content('start.submit_label', 'Send brief') }}</button>
                            <span class="font-mono text-xs text-faint">{{ content('start.reply_note', 'We reply within 1 business day.') }}</span>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>
