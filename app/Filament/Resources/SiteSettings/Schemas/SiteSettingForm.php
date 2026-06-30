<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use App\Enums\SitePage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
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
                    ]),
            ]);
    }

    /** Short single-line copy field (eyebrow, title line, label, url). */
    private static function t(string $path, string $label, ?string $placeholder = null): TextInput
    {
        return TextInput::make($path)->label($label)->placeholder($placeholder);
    }

    /** Multi-line copy field (intro, supporting paragraph). Plain text — rendered via {{ }}. */
    private static function ta(string $path, string $label, ?string $placeholder = null): Textarea
    {
        return Textarea::make($path)->label($label)->placeholder($placeholder)->rows(3)->columnSpanFull();
    }

    /** Page picker — constrains a URL field to a known SitePage (no typos, no unsafe schemes). */
    private static function pageSelect(string $path, string $label = 'Halaman tujuan'): Select
    {
        return Select::make($path)
            ->label($label)
            ->native(false)
            ->searchable()
            ->options(SitePage::options())
            ->prefixIcon('heroicon-m-link');
    }

    // ── Brand & Logo (header identity) ──────────────────────────────────────
    private static function brandTab(): Tab
    {
        return Tab::make('Brand & Logo')
            ->icon('heroicon-o-sparkles')
            ->schema([
                Section::make('Identitas (Header & Footer)')
                    ->description('Nama & logo yang dipakai header dan footer — satu sumber, selalu sama. Juga judul halaman & OG.')
                    ->icon('heroicon-m-identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('brand_name')
                            ->label('Nama brand / perusahaan')
                            ->required()
                            ->default('Creative Trees Group')
                            ->prefixIcon('heroicon-m-building-office-2')
                            ->helperText('Dipakai header + footer (sama), judul halaman, dan OG.'),
                        TextInput::make('logo_text')
                            ->label('Teks wordmark (opsional)')
                            ->placeholder('Creative Trees Group')
                            ->prefixIcon('heroicon-m-pencil')
                            ->helperText('Override teks di samping logo. Kosong = pakai nama brand lengkap.'),
                        FileUpload::make('logo_path')
                            ->label('Icon / Logo')
                            ->image()->disk('public')->directory('site/logo')->visibility('public')
                            ->imageEditor()->maxSize(2048)
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/jpeg', 'image/webp'])
                            ->helperText('Ikon brand di samping wordmark (header & footer). PNG/SVG transparan, maks 2 MB. Kosong = mark bawaan.'),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->image()->disk('public')->directory('site/favicon')->visibility('public')
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->helperText('Ikon tab browser (ICO/PNG/SVG), maks 1 MB.'),
                    ]),
                Section::make('Menu header (navigasi)')
                    ->description('Daftar menu di header. Tarik untuk mengurutkan.')
                    ->icon('heroicon-m-bars-3')
                    ->schema([
                        Repeater::make('nav_menu')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label')->label('Teks')->required()->placeholder('Work'),
                                self::pageSelect('url', 'Halaman tujuan')->required(),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah menu'),
                    ]),
                Fieldset::make('Tombol header (CTA)')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.header.cta_label', 'Teks tombol', 'Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('page_content.header.cta_url'),
                        self::t('page_content.header.close_label', 'Teks tombol tutup (menu mobile)', 'Close'),
                    ]),
            ]);
    }

    // ── Hero (home) ─────────────────────────────────────────────────────────
    private static function heroTab(): Tab
    {
        return Tab::make('Hero')
            ->icon('heroicon-o-megaphone')
            ->schema([
                Section::make('Konten hero (beranda)')
                    ->icon('heroicon-m-megaphone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_eyebrow')->label('Eyebrow')->placeholder('Digital product studio')->prefixIcon('heroicon-m-tag')->columnSpanFull(),
                        RichEditor::make('hero_title')
                            ->label('Judul hero')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/hero')->fileAttachmentsVisibility('public')
                            ->helperText('Tiap paragraf (Enter) = satu baris judul beranimasi.')
                            ->columnSpanFull(),
                        RichEditor::make('hero_subtitle')
                            ->label('Subjudul')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/hero')->fileAttachmentsVisibility('public')
                            ->helperText('Kalimat pendukung — mendukung teks kaya.')
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Tombol utama')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_cta_label')->label('Teks')->placeholder('Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('hero_cta_url'),
                    ]),
                Fieldset::make('Tombol sekunder')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_cta_secondary_label')->label('Teks')->placeholder('View work')->prefixIcon('heroicon-m-cursor-arrow-rays'),
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
                Section::make('Capabilities')->columns(2)->schema([
                    self::t('page_content.home.cap_eyebrow', 'Eyebrow', 'Capabilities'),
                    self::t('page_content.home.cap_title', 'Judul', 'Everything you need to launch and scale.'),
                    self::ta('page_content.home.cap_intro', 'Intro'),
                ]),
                Section::make('Selected work')->columns(2)->schema([
                    self::t('page_content.home.work_eyebrow', 'Eyebrow', 'Selected work'),
                    self::t('page_content.home.work_title', 'Judul', 'Proof, not promises.'),
                    self::ta('page_content.home.work_intro', 'Deskripsi', "A selection of products we've designed, built, and shipped — and the outcomes that followed."),
                    self::t('page_content.home.work_link', 'Link "semua proyek"', 'All work →'),
                ]),
                Section::make('Process')->columns(2)->schema([
                    self::t('page_content.home.process_eyebrow', 'Eyebrow', 'How we work'),
                    self::t('page_content.home.process_title', 'Judul', 'A process built to de-risk the work.'),
                    self::ta('page_content.home.process_intro', 'Intro'),
                ]),
                Section::make('Signal / testimoni')->columns(2)->schema([
                    self::t('page_content.home.signal_eyebrow', 'Eyebrow', 'Signal'),
                    self::t('page_content.home.signal_title', 'Judul', 'What partners say.'),
                    self::ta('page_content.home.signal_intro', 'Deskripsi', "Unfiltered words from the founders and teams we've embedded with."),
                ]),
                Section::make('Lainnya')->columns(2)->schema([
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
                Section::make('Halaman Work')
                    ->description('Header halaman /work.')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.work.hero_eyebrow', 'Eyebrow', 'Selected work'),
                        self::t('page_content.work.hero_title', 'Judul', 'Proof, not promises.'),
                        self::ta('page_content.work.hero_intro', 'Intro'),
                        self::ta('page_content.work.empty_message', 'Pesan saat belum ada proyek', 'Work is being published — check back soon.'),
                    ]),
                Section::make('Detail proyek — label & tombol')
                    ->description('Teks di halaman detail proyek /work/{slug}.')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        self::t('page_content.work.detail_back', 'Tombol kembali', 'Work'),
                        self::t('page_content.work.detail_client', 'Label Client', 'Client'),
                        self::t('page_content.work.detail_year', 'Label Year', 'Year'),
                        self::t('page_content.work.detail_role', 'Label Role', 'Role'),
                        self::t('page_content.work.detail_overview', 'Label Overview', 'Overview'),
                        self::t('page_content.work.detail_services', 'Label Services', 'Services'),
                        self::t('page_content.work.detail_visit', 'Tombol Visit site', 'Visit site'),
                        self::t('page_content.work.detail_gallery', 'Label Gallery', 'Gallery'),
                        self::t('page_content.work.detail_frames', 'Suffix jumlah frame', 'frames'),
                        self::t('page_content.work.more_eyebrow', 'More work — eyebrow', 'Keep looking'),
                        self::t('page_content.work.more_title', 'More work — judul', 'More work.'),
                        self::t('page_content.work.all_link', 'Link "semua proyek"', 'All work →'),
                    ]),
            ]);
    }

    private static function productsTab(): Tab
    {
        return Tab::make('Products')
            ->icon('heroicon-o-cube')
            ->schema([
                Section::make('Halaman Products')
                    ->description('Header halaman /products (tertaut dari footer) — bukan /work.')
                    ->columns(2)
                    ->schema([
                        self::t('page_content.products.hero_eyebrow', 'Eyebrow', 'Products'),
                        self::t('page_content.products.hero_line1', 'Judul baris 1', 'Starters that ship'),
                        self::t('page_content.products.hero_line2', 'Judul baris 2', 'in days, not months.'),
                        self::ta('page_content.products.hero_intro', 'Intro'),
                        self::t('page_content.products.empty_eyebrow', 'Empty-state eyebrow', 'Catalog in progress'),
                        self::ta('page_content.products.empty_message', 'Empty-state pesan'),
                        self::t('page_content.products.leadtime_label', 'Label lead-time', 'Lead-time · 1–3 weeks'),
                        self::t('page_content.products.investment_label', 'Label "Investment"', 'Investment'),
                    ]),
            ]);
    }

    // ── Services ────────────────────────────────────────────────────────────
    private static function servicesTab(): Tab
    {
        return Tab::make('Services')
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                Section::make('Hero')->columns(2)->schema([
                    self::t('page_content.services.hero_eyebrow', 'Eyebrow', 'Services'),
                    self::t('page_content.services.hero_line1', 'Judul baris 1', 'Capabilities'),
                    self::t('page_content.services.hero_line2', 'Judul baris 2', 'that compound.'),
                    self::ta('page_content.services.hero_intro', 'Intro'),
                ]),
                Section::make('Disciplines')->columns(2)->schema([
                    self::t('page_content.services.disciplines_eyebrow', 'Eyebrow', 'The disciplines'),
                    self::t('page_content.services.disciplines_label', 'Label', 'Pick one — or the full stack'),
                    self::ta('page_content.services.disciplines_intro', 'Deskripsi', 'Six disciplines held to one studio standard — engage any on its own, or stack them into a single embedded team.'),
                    self::ta('page_content.services.empty_message', 'Pesan saat belum ada layanan', 'Capabilities are being updated. Check back shortly.'),
                    self::t('page_content.services.featured_label', 'Label "Featured"', 'Featured'),
                ]),
            ]);
    }

    // ── Pricing ─────────────────────────────────────────────────────────────
    private static function pricingTab(): Tab
    {
        return Tab::make('Pricing')
            ->icon('heroicon-o-banknotes')
            ->schema([
                Section::make('Hero')->columns(2)->schema([
                    self::t('page_content.pricing.hero_eyebrow', 'Eyebrow', 'Pricing'),
                    self::ta('page_content.pricing.hero_title', 'Judul', "Engagements,\npriced honestly."),
                    self::ta('page_content.pricing.hero_intro', 'Intro'),
                ]),
                Section::make('Tiers')->columns(2)->schema([
                    self::t('page_content.pricing.tiers_eyebrow', 'Eyebrow', 'Engagement tiers'),
                    self::t('page_content.pricing.tiers_note', 'Catatan', 'Lead-based · scoped per project · no checkout'),
                    self::ta('page_content.pricing.tiers_intro', 'Deskripsi', 'Three ways to start, each scoped to the work in front of it — no packages, no checkout, no surprises.'),
                    self::ta('page_content.pricing.tiers_empty', 'Pesan saat belum ada tier', 'Engagement tiers are being finalised — check back soon.'),
                ]),
                Section::make('Included')->columns(2)->schema([
                    self::t('page_content.pricing.included_eyebrow', 'Eyebrow', 'No fine print'),
                    self::t('page_content.pricing.included_title', 'Judul', "What's always included."),
                    self::ta('page_content.pricing.included_intro', 'Intro'),
                ]),
                Section::make('FAQ')->columns(2)->schema([
                    self::t('page_content.pricing.faq_eyebrow', 'Eyebrow', 'FAQ'),
                    self::t('page_content.pricing.faq_title', 'Judul', 'Questions, answered.'),
                    self::ta('page_content.pricing.faq_intro', 'Deskripsi', 'The questions we hear most, answered straight — before you ever send a brief.'),
                ]),
            ]);
    }

    // ── Process ─────────────────────────────────────────────────────────────
    private static function processTab(): Tab
    {
        return Tab::make('Process')
            ->icon('heroicon-o-arrow-path-rounded-square')
            ->schema([
                Section::make('Hero')->columns(2)->schema([
                    self::t('page_content.process.hero_eyebrow', 'Eyebrow', 'How we work'),
                    self::t('page_content.process.hero_title', 'Judul', 'A process built to de-risk the work.'),
                    self::ta('page_content.process.hero_intro', 'Intro'),
                ]),
                Section::make('Sequence & principles')->columns(2)->schema([
                    self::t('page_content.process.sequence_eyebrow', 'Sequence eyebrow', 'The sequence'),
                    self::ta('page_content.process.sequence_intro', 'Sequence deskripsi', 'Four phases in one continuous flow — each closing the riskiest gaps before the next begins.'),
                    self::t('page_content.process.phases_label', 'Label jumlah fase', 'phases'),
                    self::t('page_content.process.principles_eyebrow', 'Principles eyebrow', 'Operating principles'),
                    self::t('page_content.process.principles_title', 'Principles judul', 'The rules that keep the work honest.'),
                    self::ta('page_content.process.principles_intro', 'Principles intro'),
                    self::ta('page_content.process.phases_empty', 'Pesan saat belum ada fase', 'The process is being documented — check back soon.'),
                    self::t('page_content.process.deliverables_label', 'Label "Deliverables"', 'Deliverables'),
                ]),
            ]);
    }

    // ── Team ────────────────────────────────────────────────────────────────
    private static function teamTab(): Tab
    {
        return Tab::make('Team')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('Hero')->columns(2)->schema([
                    self::t('page_content.team.hero_eyebrow', 'Eyebrow', 'Team'),
                    self::ta('page_content.team.hero_title', 'Judul', "The people behind\nthe work."),
                    self::ta('page_content.team.hero_intro', 'Intro'),
                    self::t('page_content.team.studio_eyebrow', 'Studio eyebrow', 'The studio'),
                    self::ta('page_content.team.studio_intro', 'Studio deskripsi', "The senior people who'll actually do your work — no account layers, no handoffs."),
                    self::ta('page_content.team.empty_message', 'Pesan saat belum ada anggota tim', 'The studio roster is being assembled. In the meantime, the work speaks for itself.'),
                    self::t('page_content.team.people_label', 'Suffix "People"', 'People'),
                    self::t('page_content.team.empty_cta', 'Tombol pada empty-state', 'View work'),
                ]),
            ]);
    }

    // ── About ───────────────────────────────────────────────────────────────
    private static function aboutTab(): Tab
    {
        return Tab::make('About')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('Hero')->columns(2)->schema([
                    self::t('page_content.about.hero_eyebrow', 'Eyebrow', 'About'),
                    TextInput::make('about_heading')->label('Judul')->placeholder('A studio built like a product team.')->columnSpanFull(),
                    RichEditor::make('about_body')
                        ->label('Isi')
                        ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/about')->fileAttachmentsVisibility('public')
                        ->helperText('Editor teks kaya.')
                        ->columnSpanFull(),
                ]),
                Section::make('Values')->columns(2)->schema([
                    self::t('page_content.about.values_eyebrow', 'Eyebrow', 'What we value'),
                    self::t('page_content.about.values_title', 'Judul', 'How we think.'),
                    self::ta('page_content.about.values_intro', 'Deskripsi', 'The handful of beliefs that shape how we design, build, and decide.'),
                ]),
                Section::make('Team & clients')->columns(2)->schema([
                    self::t('page_content.about.team_eyebrow', 'Team eyebrow', 'The team'),
                    self::t('page_content.about.team_title', 'Team judul', 'Senior, embedded, accountable.'),
                    self::ta('page_content.about.team_intro', 'Team deskripsi', 'Senior strategists, designers, and engineers who embed with your team and stay accountable end to end.'),
                    self::t('page_content.about.team_link', 'Team link', 'Meet everyone'),
                    self::t('page_content.about.clients_eyebrow', 'Clients eyebrow', 'In good company'),
                    self::ta('page_content.about.clients_intro', 'Clients deskripsi', "A few of the teams we've designed and built alongside."),
                ]),
            ]);
    }

    // ── Contact page copy ───────────────────────────────────────────────────
    private static function contactPageTab(): Tab
    {
        return Tab::make('Contact & Start')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Section::make('Halaman Contact — hero')->columns(2)->schema([
                    self::t('page_content.contact.hero_eyebrow', 'Eyebrow', 'Contact'),
                    self::t('page_content.contact.hero_title', 'Judul', "Let's talk."),
                    self::ta('page_content.contact.hero_intro', 'Intro'),
                ]),
                Section::make('Halaman Start — hero')->columns(2)->schema([
                    self::t('page_content.start.hero_eyebrow', 'Eyebrow', 'Start a project'),
                    self::ta('page_content.start.hero_title', 'Judul', "Tell us where\nyou're headed."),
                    self::ta('page_content.start.hero_intro', 'Intro'),
                    self::t('page_content.start.submit_label', 'Tombol submit', 'Send brief'),
                    self::t('page_content.start.reply_note', 'Catatan balasan', 'We reply within 1 business day.'),
                ]),
            ]);
    }

    // ── Contact data + social ───────────────────────────────────────────────
    private static function contactDataTab(): Tab
    {
        return Tab::make('Kontak & Sosial')
            ->icon('heroicon-o-at-symbol')
            ->schema([
                Section::make('Kontak')
                    ->description('Detail kontak yang tampil di footer & halaman kontak.')
                    ->icon('heroicon-m-envelope')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_email')->label('Email')->email()->prefixIcon('heroicon-m-envelope'),
                        TextInput::make('contact_phone')->label('Telepon')->tel()->prefixIcon('heroicon-m-phone'),
                        TextInput::make('contact_address')->label('Alamat')->prefixIcon('heroicon-m-map-pin')->columnSpanFull(),
                    ]),
                Section::make('Sosial media')
                    ->description('Pilih platform & isi URL profil. Tarik untuk mengurutkan.')
                    ->icon('heroicon-m-share')
                    ->schema([
                        Repeater::make('social_links')
                            ->hiddenLabel()
                            ->schema([
                                Select::make('platform')->required()->native(false)->options([
                                    'X' => 'X (Twitter)', 'LinkedIn' => 'LinkedIn', 'GitHub' => 'GitHub',
                                    'Instagram' => 'Instagram', 'Dribbble' => 'Dribbble', 'Behance' => 'Behance',
                                    'YouTube' => 'YouTube', 'Facebook' => 'Facebook', 'TikTok' => 'TikTok',
                                    'Threads' => 'Threads', 'WhatsApp' => 'WhatsApp', 'Email' => 'Email',
                                ]),
                                TextInput::make('url')->required()->url()->prefixIcon('heroicon-m-link')->placeholder('https://...'),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                            ->addActionLabel('Tambah sosial media'),
                    ]),
            ]);
    }

    // ── Stats ───────────────────────────────────────────────────────────────
    private static function statsTab(): Tab
    {
        return Tab::make('Statistik')
            ->icon('heroicon-o-chart-bar')
            ->schema([
                Section::make('Angka highlight')
                    ->description('Statistik yang ditonjolkan (mis. "120+ proyek").')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Repeater::make('stats')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('value')->label('Angka')->required()->placeholder('120+')->prefixIcon('heroicon-m-hashtag'),
                                TextInput::make('label')->label('Keterangan')->required()->placeholder('Proyek selesai'),
                            ])
                            ->columns(2)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('Tambah stat'),
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
                    ->description('Judul, deskripsi & kata kunci untuk mesin pencari dan pratinjau sosial.')
                    ->icon('heroicon-m-document-magnifying-glass')
                    ->schema([
                        TextInput::make('seo_title')->label('Judul SEO')->maxLength(60)->prefixIcon('heroicon-m-hashtag')->helperText('±50–60 karakter. Kosong = nama brand.'),
                        Textarea::make('seo_description')->label('Deskripsi meta')->rows(3)->maxLength(160)->helperText('±150–160 karakter.')->columnSpanFull(),
                        TextInput::make('seo_keywords')->label('Kata kunci')->placeholder('product studio, saas, web design')->prefixIcon('heroicon-m-key')->helperText('Pisahkan dengan koma.')->columnSpanFull(),
                        FileUpload::make('seo_image_path')->label('Gambar berbagi sosial (OG)')->image()->disk('public')->directory('site/seo')->visibility('public')->maxSize(2048)->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])->helperText('Disarankan 1200×630px.')->columnSpanFull(),
                    ]),
                Section::make('Analytics & pengindeksan')
                    ->description('Hubungkan Google Analytics dan atur perilaku indeks.')
                    ->icon('heroicon-m-chart-pie')
                    ->columns(2)
                    ->schema([
                        TextInput::make('google_analytics_id')->label('Google Analytics ID (GA4)')->placeholder('G-XXXXXXXXXX')->prefixIcon('heroicon-m-presentation-chart-line')->helperText('Measurement ID GA4. Kosong = nonaktif.')->rule('regex:/^(G|UA|GT|AW)-?[A-Z0-9\-]+$/i')->validationMessages(['regex' => 'Format ID tidak valid (contoh: G-XXXXXXXXXX).']),
                        Toggle::make('seo_noindex')->label('Sembunyikan dari mesin pencari (noindex)')->helperText('Aktifkan untuk staging/privat.')->inline(false),
                    ]),
            ]);
    }

    // ── Footer ──────────────────────────────────────────────────────────────
    private static function footerTab(): Tab
    {
        return Tab::make('Footer')
            ->icon('heroicon-o-bars-3-bottom-left')
            ->schema([
                Section::make('Ajakan (CTA) footer')
                    ->description('Pita ajakan besar di atas footer.')
                    ->icon('heroicon-m-rocket-launch')
                    ->columns(2)
                    ->schema([
                        TextInput::make('footer_cta_eyebrow')->label('Eyebrow')->placeholder("Let's build")->prefixIcon('heroicon-m-tag')->columnSpanFull(),
                        Textarea::make('footer_cta_title')->label('Judul')->placeholder("Have something\nworth building?")->helperText('Baris baru memecah judul.')->rows(2)->columnSpanFull(),
                        Textarea::make('footer_cta_body')->label('Deskripsi')->placeholder("Tell us where you're headed.")->rows(2)->columnSpanFull(),
                        TextInput::make('footer_cta_label')->label('Teks tombol')->placeholder('Start a project')->prefixIcon('heroicon-m-cursor-arrow-rays'),
                        self::pageSelect('footer_cta_url'),
                    ]),
                Section::make('Identitas footer')
                    ->description('Tagline, lokasi, copyright & watermark.')
                    ->icon('heroicon-m-bars-3-bottom-left')
                    ->columns(2)
                    ->schema([
                        RichEditor::make('footer_tagline')
                            ->label('Tagline')
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('site/footer')->fileAttachmentsVisibility('public')
                            ->helperText('Teks kaya di bawah logo footer.')
                            ->columnSpanFull(),
                        TextInput::make('footer_location')->label('Lokasi')->placeholder('Jakarta · Remote-first')->prefixIcon('heroicon-m-map-pin'),
                        TextInput::make('footer_copyright')->label('Nama copyright')->placeholder('Creative Trees Group')->helperText('Tahun otomatis: © '.date('Y').' + teks ini. Kosong = nama brand.')->prefixIcon('heroicon-m-calendar'),
                        TextInput::make('footer_watermark')->label('Teks watermark (opsional)')->placeholder('Creative Trees Group')->helperText('Teks besar di latar footer. Kosong = nama brand.')->prefixIcon('heroicon-m-sparkles')->columnSpanFull(),
                        self::t('page_content.footer.contact_label', 'Judul kolom kontak', 'Contact')->columnSpanFull(),
                    ]),
            ]);
    }
}
