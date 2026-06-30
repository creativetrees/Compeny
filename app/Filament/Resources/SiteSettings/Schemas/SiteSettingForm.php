<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use App\Enums\SitePage;
use App\Models\SiteSetting;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        self::brandTab(),
                        self::heroTab(),
                        self::homeTab(),
                        self::workTab(),
                        self::productsTab(),
                        self::servicesTab(),
                        self::pricingTab(),
                        self::processTab(),
                        self::teamTab(),
                        self::aboutTab(),
                        self::contactPageTab(),
                        self::contactDataTab(),
                        self::statsTab(),
                        self::seoTab(),
                        self::footerTab(),
                        self::systemTab(),
                    ]),
            ]);
    }

    /** Short single-line copy field (eyebrow, title line, label, url) — auto-iconed,
        and pre-filled with its default so the admin sees the current text (edit only if needed). */
    private static function t(string $path, string $label, ?string $placeholder = null, ?string $icon = null): TextInput
    {
        $field = TextInput::make($path)->label($label)->placeholder($placeholder)->prefixIcon($icon ?? self::guessIcon($label));

        if (filled($placeholder)) {
            $field->formatStateUsing(fn ($state) => filled($state) ? $state : $placeholder);
        }

        return $field;
    }

    /** Pick a role-appropriate prefix icon from the field label so every text field is iconed consistently. */
    private static function guessIcon(string $label): string
    {
        $l = mb_strtolower($label);

        return match (true) {
            str_contains($l, 'eyebrow') => 'heroicon-m-tag',
            str_contains($l, 'url') => 'heroicon-m-link',
            str_contains($l, 'link') => 'heroicon-m-arrow-top-right-on-square',
            str_contains($l, 'tombol') || str_contains($l, 'cta') || str_contains($l, 'submit') || str_contains($l, 'button') => 'heroicon-m-cursor-arrow-rays',
            str_contains($l, 'judul') || str_contains($l, 'title') => 'heroicon-m-bars-3-bottom-left',
            str_contains($l, 'badge') => 'heroicon-m-check-badge',
            str_contains($l, 'label') || str_contains($l, 'keterangan') => 'heroicon-m-bookmark',
            str_contains($l, 'catatan') || str_contains($l, 'note') => 'heroicon-m-chat-bubble-bottom-center-text',
            str_contains($l, 'suffix') || str_contains($l, 'jumlah') => 'heroicon-m-hashtag',
            str_contains($l, 'pesan') || str_contains($l, 'message') || str_contains($l, 'empty') => 'heroicon-m-information-circle',
            default => 'heroicon-m-pencil',
        };
    }

    /** Multi-line RICH copy field (intro, description, message) — large editor. */
    private static function ta(string $path, string $label, ?string $placeholder = null): RichEditor
    {
        return RichEditor::make($path)->label($label)
            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/richtext')->fileAttachmentsVisibility('public')
            ->columnSpanFull();
    }

    /** Plain multi-line field — for multi-line titles rendered with nl2br on the frontend. */
    private static function taPlain(string $path, string $label, ?string $placeholder = null): Textarea
    {
        return Textarea::make($path)->label($label)->placeholder($placeholder)->rows(3)->columnSpanFull();
    }

    /** Rich editor (long-form description). */
    private static function rich(string $path, string $label): RichEditor
    {
        return RichEditor::make($path)->label($label)->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/richtext')->fileAttachmentsVisibility('public')->columnSpanFull();
    }

    /** Page picker — constrains a URL field to a known SitePage (no typos, no unsafe schemes). */
    private static function pageSelect(string $path, string $label = 'Target page'): Select
    {
        return Select::make($path)
            ->label($label)
            ->native(false)
            ->searchable()
            ->options(SitePage::options())
            ->prefixIcon('heroicon-m-link');
    }

    // ── System: maintenance + error pages ──────────────────────────────────
    private static function systemTab(): Tab
    {
        return Tab::make('System')
            ->icon('heroicon-o-wrench-screwdriver')
            ->schema([
                Section::make('Maintenance mode')
                    ->description('When on, public visitors see the maintenance page. Admins and signed-in users still have access to turn it off. No artisan needed.')
                    ->icon('heroicon-m-wrench')
                    ->columns(2)
                    ->schema([
                        Toggle::make('page_content.system.maintenance')
                            ->label('Enable maintenance mode')
                            ->helperText('Public visitors see the maintenance page when ON. Applies instantly — no Save needed.')
                            ->inline(false)->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function ($state): void {
                                // Persist this one flag immediately (no Save needed). Reads the
                                // current row so other unsaved form edits are not overwritten.
                                $setting = SiteSetting::query()->firstOrCreate(['id' => 1]);
                                $pc = $setting->page_content ?? [];
                                data_set($pc, 'system.maintenance', (bool) $state);
                                $setting->page_content = $pc;
                                $setting->save();

                                Notification::make()
                                    ->title($state ? 'Maintenance mode on' : 'Maintenance mode off')
                                    ->body($state
                                        ? 'Visitors now see the maintenance page. Admins and signed-in users still have access.'
                                        : 'The site is back online for everyone.')
                                    ->icon($state ? 'heroicon-o-wrench-screwdriver' : 'heroicon-o-check-circle')
                                    ->iconColor($state ? 'warning' : 'success')
                                    ->color($state ? 'warning' : 'success')
                                    ->duration(5000)
                                    ->send();
                            }),
                        self::t('page_content.system.maint_title', 'Title', "We'll be right back"),
                        Grid::make(2)->columnSpanFull()->schema([
                            DateTimePicker::make('page_content.system.maint_start')->label('Start')->seconds(false)->native(false)->prefixIcon('heroicon-m-play')->helperText('Optional. Leave empty if you don’t want to show a schedule.'),
                            DateTimePicker::make('page_content.system.maint_end')->label('End')->seconds(false)->native(false)->prefixIcon('heroicon-m-flag')->helperText('Optional. Estimated time the site is back online.'),
                        ]),
                        self::ta('page_content.system.maint_message', 'Message')->formatStateUsing(fn ($state) => filled($state) ? $state : 'We’re shipping a quick upgrade, so the site is briefly offline. No action needed — it’ll be back to normal shortly.'),
                    ]),
                Section::make('Error pages')
                    ->description('Text for each error page — pre-filled with defaults, edit only if needed. Still shows even if the DB is down (crash-safe).')
                    ->icon('heroicon-m-exclamation-triangle')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.system.e401_title', '401 — title', 'Sign in to continue.'),
                        self::t('page_content.system.e401_message', '401 — message', 'This page needs a verified session. Sign in, then head back to where you were going.'),
                        self::t('page_content.system.e403_title', '403 — title', 'You can’t open this.'),
                        self::t('page_content.system.e403_message', '403 — message', 'This page is locked to your account. If you think that’s a mistake, get in touch and we’ll sort it out.'),
                        self::t('page_content.system.e404_title', '404 — title', 'This page isn’t here.'),
                        self::t('page_content.system.e404_message', '404 — message', 'The page you’re after moved, was renamed, or never existed. Everything still standing is one click away.'),
                        self::t('page_content.system.e419_title', '419 — title', 'Your session expired.'),
                        self::t('page_content.system.e419_message', '419 — message', 'For security, the page sat idle too long. Refresh it and submit again — nothing you typed was lost.'),
                        self::t('page_content.system.e429_title', '429 — title', 'Slow down a moment.'),
                        self::t('page_content.system.e429_message', '429 — message', 'You’ve sent a lot of requests in a short time. Wait a few seconds, then try again.'),
                        self::t('page_content.system.e500_title', '500 — title', 'Something broke on our end.'),
                        self::t('page_content.system.e500_message', '500 — message', 'That’s on us, not you. The team is alerted automatically — give it a moment, then try again.'),
                        self::t('page_content.system.e503_title', '503 — title', 'We’re temporarily offline.'),
                        self::t('page_content.system.e503_message', '503 — message', 'The service is briefly unavailable — likely heavy load or a quick restart. Give it a moment and try again.'),
                    ]),
            ]);
    }

    // ── Brand & Logo (header identity) ──────────────────────────────────────
    private static function brandTab(): Tab
    {
        return Tab::make('Brand & Logo')
            ->icon('heroicon-o-sparkles')
            ->schema([
                Section::make('Identity (Header & Footer)')
                    ->description('Name & logo used in the header and footer — one source, always the same. Also the page title & OG.')
                    ->icon('heroicon-m-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('brand_name')
                            ->label('Brand / company name')
                            ->required()
                            ->default('Creative Trees Group')
                            ->prefixIcon('heroicon-m-building-office-2')
                            ->helperText('Used in header + footer (same), page title, and OG.'),
                        TextInput::make('logo_text')
                            ->label('Wordmark text (optional)')
                            ->placeholder('Creative Trees Group')
                            ->prefixIcon('heroicon-m-pencil')
                            ->helperText('Overrides the text next to the logo. Empty = use the full brand name.'),
                        FileUpload::make('logo_path')
                            ->label('Icon / Logo')
                            ->image()->disk('public')->directory('site/logo')->visibility('public')
                            ->imageEditor()->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/jpeg', 'image/webp'])
                            ->helperText('Brand icon next to the wordmark (header & footer). Transparent PNG/SVG, max 2 MB. Empty = default mark.'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()->disk('public')->directory('site/favicon')->visibility('public')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->helperText('Browser tab icon (ICO/PNG/SVG), max 1 MB.'),
                    ]),
                Section::make('Header menu (navigation)')
                    ->description('Header menu items. Drag to reorder.')
                    ->icon('heroicon-m-bars-3')
                    ->schema([
                        Repeater::make('nav_menu')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label')->label('Text')->required()->placeholder('Work')->prefixIcon('heroicon-m-bookmark'),
                                self::pageSelect('url', 'Target page')->required(),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Add menu item'),
                    ]),
                Fieldset::make('Header button (CTA)')
                    ->columns(3)
                    ->schema([
                        self::t('page_content.header.cta_label', 'Button text', 'Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('page_content.header.cta_url'),
                        self::t('page_content.header.close_label', 'Close button (mobile menu)', 'Close')->prefixIcon('heroicon-m-x-mark'),
                    ]),
            ]);
    }

    // ── Hero (home) ─────────────────────────────────────────────────────────
    private static function heroTab(): Tab
    {
        return Tab::make('Hero')
            ->icon('heroicon-o-megaphone')
            ->schema([
                Section::make('Hero content (home)')
                    ->icon('heroicon-m-megaphone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_eyebrow')->label('Eyebrow')->placeholder('Digital product studio')->prefixIcon('heroicon-m-tag')->columnSpanFull(),
                        RichEditor::make('hero_title')
                            ->label('Hero title')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/hero')->fileAttachmentsVisibility('public')
                            ->helperText('Each paragraph (Enter) = one animated title line.')
                            ->columnSpanFull(),
                        RichEditor::make('hero_subtitle')
                            ->label('Subtitle')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/hero')->fileAttachmentsVisibility('public')
                            ->helperText('Supporting sentence — supports rich text.')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Primary button')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_cta_label')->label('Text')->placeholder('Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('hero_cta_url'),
                    ]),
                Fieldset::make('Secondary button')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_cta_secondary_label')->label('Text')->placeholder('View work')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('hero_cta_secondary_url'),
                    ]),
            ]);
    }

    // ── Home sections ───────────────────────────────────────────────────────
    private static function homeTab(): Tab
    {
        return Tab::make('Home')
            ->icon('heroicon-o-home')
            ->schema([
                Section::make('Capabilities')->icon('heroicon-m-squares-2x2')->columns(2)->schema([
                    self::t('page_content.home.cap_eyebrow', 'Eyebrow', 'Capabilities'),
                    self::t('page_content.home.cap_title', 'Title', 'Everything you need to launch and scale.'),
                    self::ta('page_content.home.cap_intro', 'Intro'),
                ]),
                Section::make('Selected work')->icon('heroicon-m-briefcase')->columns(3)->schema([
                    self::t('page_content.home.work_eyebrow', 'Eyebrow', 'Selected work'),
                    self::t('page_content.home.work_title', 'Title', 'Proof, not promises.'),
                    self::t('page_content.home.work_link', '"All work" link', 'All work →'),
                    self::ta('page_content.home.work_intro', 'Description', "A selection of products we've designed, built, and shipped — and the outcomes that followed."),
                ]),
                Section::make('Process')->icon('heroicon-m-arrow-path-rounded-square')->columns(2)->schema([
                    self::t('page_content.home.process_eyebrow', 'Eyebrow', 'How we work'),
                    self::t('page_content.home.process_title', 'Title', 'A process built to de-risk the work.'),
                    self::ta('page_content.home.process_intro', 'Intro'),
                ]),
                Section::make('Signal / testimonials')->icon('heroicon-m-chat-bubble-left-right')->columns(2)->schema([
                    self::t('page_content.home.signal_eyebrow', 'Eyebrow', 'Signal'),
                    self::t('page_content.home.signal_title', 'Title', 'What partners say.'),
                    self::ta('page_content.home.signal_intro', 'Description', "Unfiltered words from the founders and teams we've embedded with."),
                ]),
                Section::make('Other')->icon('heroicon-m-ellipsis-horizontal-circle')->columns(2)->schema([
                    self::t('page_content.home.trusted_eyebrow', 'Trusted-by eyebrow', 'Trusted by innovative teams'),
                ]),
            ]);
    }

    // ── Work / Products ─────────────────────────────────────────────────────
    private static function workTab(): Tab
    {
        return Tab::make('Work')
            ->icon('heroicon-o-briefcase')
            ->schema([
                Section::make('Work page')
                    ->description('Header for the /work page.')
                    ->icon('heroicon-m-briefcase')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.work.hero_eyebrow', 'Eyebrow', 'Selected work'),
                        self::t('page_content.work.hero_title', 'Title', 'Proof, not promises.'),
                        self::rich('page_content.work.hero_intro', 'Intro'),
                        self::ta('page_content.work.empty_message', 'Message when there are no projects yet', 'Work is being published — check back soon.'),
                    ]),
                Section::make('Project detail — labels & buttons')
                    ->description('Text on the project detail page /work/{slug}.')
                    ->icon('heroicon-m-rectangle-stack')
                    ->collapsed()
                    ->columns(3)
                    ->schema([
                        self::t('page_content.work.detail_back', 'Back button', 'Work'),
                        self::t('page_content.work.detail_client', 'Client label', 'Client'),
                        self::t('page_content.work.detail_year', 'Year label', 'Year'),
                        self::t('page_content.work.detail_role', 'Role label', 'Role'),
                        self::t('page_content.work.detail_overview', 'Overview label', 'Overview'),
                        self::t('page_content.work.detail_services', 'Services label', 'Services'),
                        self::t('page_content.work.detail_visit', 'Visit site button', 'Visit site'),
                        self::t('page_content.work.detail_gallery', 'Gallery label', 'Gallery'),
                        self::t('page_content.work.detail_frames', 'Frame count suffix', 'frames'),
                        self::t('page_content.work.more_eyebrow', 'More work — eyebrow', 'Keep looking'),
                        self::t('page_content.work.more_title', 'More work — title', 'More work.'),
                        self::t('page_content.work.all_link', '"All work" link', 'All work →'),
                    ]),
            ]);
    }

    private static function productsTab(): Tab
    {
        return Tab::make('Products')
            ->icon('heroicon-o-cube')
            ->schema([
                Section::make('Products page')
                    ->description('Header for the /products page (linked from the footer) — not /work.')
                    ->icon('heroicon-m-cube')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.products.hero_eyebrow', 'Eyebrow', 'Products'),
                        self::t('page_content.products.hero_line1', 'Title line 1', 'Starters that ship'),
                        self::t('page_content.products.hero_line2', 'Title line 2', 'in days, not months.'),
                        self::t('page_content.products.empty_eyebrow', 'Empty-state eyebrow', 'Catalog in progress'),
                        self::t('page_content.products.leadtime_label', 'Lead-time label', 'Lead-time · 1–3 weeks'),
                        self::t('page_content.products.investment_label', 'Label "Investment"', 'Investment'),
                        self::rich('page_content.products.hero_intro', 'Intro'),
                        self::ta('page_content.products.empty_message', 'Empty-state message'),
                    ]),
            ]);
    }

    // ── Services ────────────────────────────────────────────────────────────
    private static function servicesTab(): Tab
    {
        return Tab::make('Services')
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                Section::make('Hero')->icon('heroicon-m-megaphone')->columns(2)->schema([
                    self::t('page_content.services.hero_eyebrow', 'Eyebrow', 'Services'),
                    self::t('page_content.services.hero_line1', 'Title line 1', 'Capabilities'),
                    self::t('page_content.services.hero_line2', 'Title line 2', 'that compound.'),
                    self::rich('page_content.services.hero_intro', 'Intro'),
                ]),
                Section::make('Disciplines')->icon('heroicon-m-squares-plus')->columns(2)->schema([
                    self::t('page_content.services.disciplines_eyebrow', 'Eyebrow', 'The disciplines'),
                    self::t('page_content.services.disciplines_label', 'Label', 'Pick one — or the full stack'),
                    self::t('page_content.services.featured_label', 'Label "Featured"', 'Featured'),
                    self::ta('page_content.services.disciplines_intro', 'Description', 'Six disciplines held to one studio standard — engage any on its own, or stack them into a single embedded team.'),
                    self::ta('page_content.services.empty_message', 'Message when there are no services yet', 'Capabilities are being updated. Check back shortly.'),
                ]),
            ]);
    }

    // ── Pricing ─────────────────────────────────────────────────────────────
    private static function pricingTab(): Tab
    {
        return Tab::make('Pricing')
            ->icon('heroicon-o-banknotes')
            ->schema([
                Section::make('Hero')->icon('heroicon-m-megaphone')->columns(2)->schema([
                    self::t('page_content.pricing.hero_eyebrow', 'Eyebrow', 'Pricing'),
                    self::taPlain('page_content.pricing.hero_title', 'Title', "Engagements,\npriced honestly."),
                    self::rich('page_content.pricing.hero_intro', 'Intro'),
                ]),
                Section::make('Tiers')->icon('heroicon-m-squares-2x2')->columns(2)->schema([
                    self::t('page_content.pricing.tiers_eyebrow', 'Eyebrow', 'Engagement tiers'),
                    self::t('page_content.pricing.tiers_note', 'Note', 'Lead-based · scoped per project · no checkout'),
                    self::t('page_content.pricing.popular_label', 'Badge "Most popular"', 'Most popular'),
                    self::t('page_content.pricing.tier_cta', 'Button on the tier card', 'Start a project'),
                    self::pageSelect('page_content.pricing.tier_cta_url', 'Tier button URL'),
                    self::t('page_content.pricing.studio_note', 'Studio note (before the services list)', 'Every engagement draws on the full studio —'),
                    self::ta('page_content.pricing.tiers_intro', 'Description', 'Three ways to start, each scoped to the work in front of it — no packages, no checkout, no surprises.'),
                    self::ta('page_content.pricing.tiers_empty', 'Message when there are no tiers yet', 'Engagement tiers are being finalised — check back soon.'),
                ]),
                Section::make('Included')->icon('heroicon-m-check-circle')->columns(2)->schema([
                    self::t('page_content.pricing.included_eyebrow', 'Eyebrow', 'No fine print'),
                    self::t('page_content.pricing.included_title', 'Title', "What's always included."),
                    self::ta('page_content.pricing.included_intro', 'Intro'),
                ]),
                Section::make('FAQ')->icon('heroicon-m-question-mark-circle')->columns(2)->schema([
                    self::t('page_content.pricing.faq_eyebrow', 'Eyebrow', 'FAQ'),
                    self::t('page_content.pricing.faq_title', 'Title', 'Questions, answered.'),
                    self::ta('page_content.pricing.faq_intro', 'Description', 'The questions we hear most, answered straight — before you ever send a brief.'),
                ]),
            ]);
    }

    // ── Process ─────────────────────────────────────────────────────────────
    private static function processTab(): Tab
    {
        return Tab::make('Process')
            ->icon('heroicon-o-arrow-path-rounded-square')
            ->schema([
                Section::make('Hero')->icon('heroicon-m-megaphone')->columns(2)->schema([
                    self::t('page_content.process.hero_eyebrow', 'Eyebrow', 'How we work'),
                    self::t('page_content.process.hero_title', 'Title', 'A process built to de-risk the work.'),
                    self::rich('page_content.process.hero_intro', 'Intro'),
                ]),
                Section::make('Sequence & principles')->icon('heroicon-m-list-bullet')->columns(2)->schema([
                    self::t('page_content.process.sequence_eyebrow', 'Sequence eyebrow', 'The sequence'),
                    self::t('page_content.process.phases_label', 'Phase count label', 'phases'),
                    self::t('page_content.process.principles_eyebrow', 'Principles eyebrow', 'Operating principles'),
                    self::t('page_content.process.principles_title', 'Principles title', 'The rules that keep the work honest.'),
                    self::t('page_content.process.deliverables_label', 'Label "Deliverables"', 'Deliverables'),
                    self::ta('page_content.process.sequence_intro', 'Sequence description', 'Four phases in one continuous flow — each closing the riskiest gaps before the next begins.'),
                    self::ta('page_content.process.principles_intro', 'Principles intro'),
                    self::ta('page_content.process.phases_empty', 'Message when there are no phases yet', 'The process is being documented — check back soon.'),
                ]),
            ]);
    }

    // ── Team ────────────────────────────────────────────────────────────────
    private static function teamTab(): Tab
    {
        return Tab::make('Team')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('Hero')->icon('heroicon-m-megaphone')->columns(2)->schema([
                    self::t('page_content.team.hero_eyebrow', 'Eyebrow', 'Team'),
                    self::t('page_content.team.studio_eyebrow', 'Studio eyebrow', 'The studio'),
                    self::t('page_content.team.people_label', 'Suffix "People"', 'People'),
                    self::t('page_content.team.empty_cta', 'Button on the empty state', 'View work'),
                    self::taPlain('page_content.team.hero_title', 'Title', "The people behind\nthe work."),
                    self::rich('page_content.team.hero_intro', 'Intro'),
                    self::ta('page_content.team.studio_intro', 'Studio description', "The senior people who'll actually do your work — no account layers, no handoffs."),
                    self::ta('page_content.team.empty_message', 'Message when there are no team members yet', 'The studio roster is being assembled. In the meantime, the work speaks for itself.'),
                ]),
            ]);
    }

    // ── About ───────────────────────────────────────────────────────────────
    private static function aboutTab(): Tab
    {
        return Tab::make('About')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('Hero')->icon('heroicon-m-megaphone')->columns(2)->schema([
                    self::t('page_content.about.hero_eyebrow', 'Eyebrow', 'About'),
                    TextInput::make('about_heading')->label('Title')->placeholder('A studio built like a product team.')->prefixIcon('heroicon-m-bars-3-bottom-left'),
                    RichEditor::make('about_body')
                        ->label('Body')
                        ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/about')->fileAttachmentsVisibility('public')
                        ->helperText('Rich text editor.')
                        ->columnSpanFull(),
                ]),
                Section::make('Values')->icon('heroicon-m-sparkles')->columns(2)->schema([
                    self::t('page_content.about.values_eyebrow', 'Eyebrow', 'What we value'),
                    self::t('page_content.about.values_title', 'Title', 'How we think.'),
                    self::ta('page_content.about.values_intro', 'Description', 'The handful of beliefs that shape how we design, build, and decide.'),
                ]),
                Section::make('Team & clients')->icon('heroicon-m-user-group')->columns(2)->schema([
                    self::t('page_content.about.team_eyebrow', 'Team eyebrow', 'The team'),
                    self::t('page_content.about.team_title', 'Team title', 'Senior, embedded, accountable.'),
                    self::t('page_content.about.team_link', 'Team link', 'Meet everyone'),
                    self::t('page_content.about.clients_eyebrow', 'Clients eyebrow', 'In good company'),
                    self::ta('page_content.about.team_intro', 'Team description', 'Senior strategists, designers, and engineers who embed with your team and stay accountable end to end.'),
                    self::ta('page_content.about.clients_intro', 'Clients description', "A few of the teams we've designed and built alongside."),
                ]),
            ]);
    }

    // ── Contact page copy ───────────────────────────────────────────────────
    private static function contactPageTab(): Tab
    {
        return Tab::make('Contact & Start')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Section::make('Contact page — hero')->icon('heroicon-m-chat-bubble-left-right')->columns(2)->schema([
                    self::t('page_content.contact.hero_eyebrow', 'Eyebrow', 'Contact'),
                    self::t('page_content.contact.hero_title', 'Title', "Let's talk."),
                    self::rich('page_content.contact.hero_intro', 'Intro'),
                ]),
                Section::make('Start page — hero')->icon('heroicon-m-paper-airplane')->columns(3)->schema([
                    self::t('page_content.start.hero_eyebrow', 'Eyebrow', 'Start a project'),
                    self::t('page_content.start.submit_label', 'Submit button', 'Send brief'),
                    self::t('page_content.start.reply_note', 'Reply note', 'We reply within 1 business day.'),
                    self::taPlain('page_content.start.hero_title', 'Title', "Tell us where\nyou're headed."),
                    self::rich('page_content.start.hero_intro', 'Intro'),
                ]),
            ]);
    }

    // ── Contact data + social ───────────────────────────────────────────────
    private static function contactDataTab(): Tab
    {
        return Tab::make('Contact & Social')
            ->icon('heroicon-o-at-symbol')
            ->schema([
                Section::make('Contact')
                    ->description('Contact details shown in the footer & contact page.')
                    ->icon('heroicon-m-envelope')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_email')->label('Email')->email()->prefixIcon('heroicon-m-envelope'),
                        TextInput::make('contact_phone')->label('Phone')->tel()->prefixIcon('heroicon-m-phone'),
                        TextInput::make('contact_address')->label('Address')->prefixIcon('heroicon-m-map-pin')->columnSpanFull(),
                        TextInput::make('page_content.system.notify_email')->label('Notification recipient email (lead form)')->email()->placeholder('support@creativetreesgroup.com')->helperText('Where new lead emails are sent. Empty = use the contact email above.')->prefixIcon('heroicon-m-inbox-arrow-down')->columnSpanFull(),
                    ]),
                Section::make('Social media')
                    ->description('Pick a platform & enter the profile URL. Drag to reorder.')
                    ->icon('heroicon-m-share')
                    ->schema([
                        Repeater::make('social_links')
                            ->hiddenLabel()
                            ->schema([
                                Select::make('platform')->required()->native(false)->prefixIcon('heroicon-m-globe-alt')->options([
                                    'X' => 'X (Twitter)', 'LinkedIn' => 'LinkedIn', 'GitHub' => 'GitHub',
                                    'Instagram' => 'Instagram', 'Dribbble' => 'Dribbble', 'Behance' => 'Behance',
                                    'YouTube' => 'YouTube', 'Facebook' => 'Facebook', 'TikTok' => 'TikTok',
                                    'Threads' => 'Threads', 'WhatsApp' => 'WhatsApp', 'Email' => 'Email',
                                ]),
                                TextInput::make('url')->required()->url()->prefixIcon('heroicon-m-link')->placeholder('https://...'),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                            ->addActionLabel('Add social media'),
                    ]),
            ]);
    }

    // ── Stats ───────────────────────────────────────────────────────────────
    private static function statsTab(): Tab
    {
        return Tab::make('Statistics')
            ->icon('heroicon-o-chart-bar')
            ->schema([
                Section::make('Highlight numbers')
                    ->description('Highlighted statistics (e.g. "120+ projects").')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Repeater::make('stats')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('value')->label('Number')->required()->placeholder('120+')->prefixIcon('heroicon-m-hashtag'),
                                TextInput::make('label')->label('Label')->required()->placeholder('Projects completed')->prefixIcon('heroicon-m-bookmark'),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Add stat'),
                    ]),
            ]);
    }

    // ── SEO + analytics ─────────────────────────────────────────────────────
    private static function seoTab(): Tab
    {
        return Tab::make('SEO')
            ->icon('heroicon-o-magnifying-glass')
            ->schema([
                Section::make('Metadata')
                    ->description('Title, description & keywords for search engines and social previews.')
                    ->icon('heroicon-m-document-magnifying-glass')
                    ->schema([
                        TextInput::make('seo_title')->label('SEO title')->maxLength(60)->prefixIcon('heroicon-m-hashtag')->helperText('±50–60 characters. Empty = brand name.'),
                        Textarea::make('seo_description')->label('Meta description')->rows(3)->maxLength(160)->helperText('±150–160 characters.')->columnSpanFull(),
                        TextInput::make('seo_keywords')->label('Keywords')->placeholder('product studio, saas, web design')->prefixIcon('heroicon-m-key')->helperText('Separate with commas.')->columnSpanFull(),
                        FileUpload::make('seo_image_path')->label('Social share image (OG)')->image()->disk('public')->directory('site/seo')->visibility('public')->maxSize(2048)->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])->helperText('Recommended 1200×630px.')->columnSpanFull(),
                    ]),
                Section::make('Analytics & indexing')
                    ->description('Connect Google Analytics and control indexing behavior.')
                    ->icon('heroicon-m-chart-pie')
                    ->columns(2)
                    ->schema([
                        TextInput::make('google_analytics_id')->label('Google Analytics ID (GA4)')->placeholder('G-XXXXXXXXXX')->prefixIcon('heroicon-m-presentation-chart-line')->helperText('GA4 Measurement ID. Empty = disabled.')->rule('regex:/^(G|UA|GT|AW)-?[A-Z0-9\-]+$/i')->validationMessages(['regex' => 'Invalid ID format (e.g. G-XXXXXXXXXX).']),
                        Toggle::make('seo_noindex')->label('Hide from search engines (noindex)')->helperText('Enable for staging/private.')->inline(false),
                    ]),
            ]);
    }

    // ── Footer ──────────────────────────────────────────────────────────────
    private static function footerTab(): Tab
    {
        return Tab::make('Footer')
            ->icon('heroicon-o-bars-3-bottom-left')
            ->schema([
                Section::make('Footer call to action (CTA)')
                    ->description('Large call-to-action band above the footer.')
                    ->icon('heroicon-m-rocket-launch')
                    ->columns(2)
                    ->schema([
                        TextInput::make('footer_cta_eyebrow')->label('Eyebrow')->placeholder("Let's build")->prefixIcon('heroicon-m-tag')->columnSpanFull(),
                        Textarea::make('footer_cta_title')->label('Title')->placeholder("Have something\nworth building?")->helperText('New lines split the title.')->rows(2)->columnSpanFull(),
                        TextInput::make('footer_cta_label')->label('Button text')->placeholder('Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('footer_cta_url'),
                        RichEditor::make('footer_cta_body')->label('Description')->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/footer')->fileAttachmentsVisibility('public')->columnSpanFull(),
                    ]),
                Section::make('Footer identity')
                    ->description('Tagline, location, copyright & watermark.')
                    ->icon('heroicon-m-bars-3-bottom-left')
                    ->columns(2)
                    ->schema([
                        RichEditor::make('footer_tagline')
                            ->label('Tagline')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/footer')->fileAttachmentsVisibility('public')
                            ->helperText('Rich text below the footer logo.')
                            ->columnSpanFull(),
                        TextInput::make('footer_location')->label('Location')->placeholder('Jakarta · Remote-first')->prefixIcon('heroicon-m-map-pin'),
                        TextInput::make('footer_copyright')->label('Copyright name')->placeholder('Creative Trees Group')->helperText('Year is automatic: © '.date('Y').' + this text. Empty = brand name.')->prefixIcon('heroicon-m-calendar'),
                        TextInput::make('footer_watermark')->label('Watermark text (optional)')->placeholder('Creative Trees Group')->helperText('Large text in the footer background. Empty = brand name.')->prefixIcon('heroicon-m-sparkles')->columnSpanFull(),
                        self::t('page_content.footer.contact_label', 'Contact column title', 'Contact')->columnSpanFull(),
                    ]),
            ]);
    }
}
