<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

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
                        // ── HEADER: brand identity used in the top nav, page title & OG ──
                        Tab::make('Brand & Logo')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Identitas (Header)')
                                    ->description('Nama & logo yang tampil di header dan dipakai untuk judul halaman / berbagi sosial. Khusus identitas — bukan footer atau hero.')
                                    ->icon('heroicon-m-identification')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('brand_name')
                                            ->label('Nama brand / perusahaan')
                                            ->required()
                                            ->default('Creative Trees Group')
                                            ->prefixIcon('heroicon-m-building-office-2')
                                            ->helperText('Nama lengkap — dipakai di judul halaman, OG tag, dan footer.'),
                                        TextInput::make('logo_text')
                                            ->label('Teks wordmark (opsional)')
                                            ->placeholder('Creative Trees Group')
                                            ->prefixIcon('heroicon-m-pencil')
                                            ->helperText('Override teks di samping logo (header & footer). Kosong = pakai nama brand lengkap.'),
                                        FileUpload::make('logo_path')
                                            ->label('Logo perusahaan')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site/logo')
                                            ->visibility('public')
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/jpeg', 'image/webp'])
                                            ->helperText('PNG/SVG transparan, maks 2 MB. Kosong = pakai mark bawaan.'),
                                        FileUpload::make('favicon_path')
                                            ->label('Favicon')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site/favicon')
                                            ->visibility('public')
                                            ->maxSize(1024)
                                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                            ->helperText('Ikon tab browser (ICO/PNG/SVG, mis. 512×512), maks 1 MB. Kosong = favicon default.'),
                                    ]),
                            ]),

                        // ── HERO: the landing headline block (homepage only) ──
                        Tab::make('Hero')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('Konten hero')
                                    ->description('Blok pembuka di halaman utama. Hanya untuk hero — terpisah dari header & footer.')
                                    ->icon('heroicon-m-megaphone')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('hero_eyebrow')
                                            ->label('Eyebrow')
                                            ->placeholder('Digital product studio')
                                            ->prefixIcon('heroicon-m-tag')
                                            ->helperText('Label kecil di atas judul hero.')
                                            ->columnSpanFull(),
                                        RichEditor::make('hero_title')
                                            ->label('Judul hero')
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('site/hero')
                                            ->fileAttachmentsVisibility('public')
                                            ->helperText('Tiap paragraf (tekan Enter) menjadi satu baris judul beranimasi. Format inline tidak tampil pada judul beranimasi.')
                                            ->columnSpanFull(),
                                        RichEditor::make('hero_subtitle')
                                            ->label('Subjudul')
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('site/hero')
                                            ->fileAttachmentsVisibility('public')
                                            ->helperText('Kalimat pendukung di bawah judul — mendukung teks kaya (tebal, miring, tautan).')
                                            ->columnSpanFull(),
                                    ]),
                                Fieldset::make('Tombol aksi (CTA) hero')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('hero_cta_label')
                                            ->label('Teks tombol')
                                            ->placeholder('Start a project')
                                            ->prefixIcon('heroicon-m-cursor-arrow-rays'),
                                        TextInput::make('hero_cta_url')
                                            ->label('URL tombol')
                                            ->placeholder('/start')
                                            ->prefixIcon('heroicon-m-link')
                                            ->rule('regex:/^(https?:\/\/|\/|#|mailto:|tel:)/i')
                                            ->validationMessages(['regex' => 'Gunakan path relatif (/...) atau URL http(s)://, mailto:, tel:.']),
                                    ]),
                            ]),

                        // ── ABOUT: studio story, rich text ──
                        Tab::make('About')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Tentang studio')
                                    ->description('Narasi profil perusahaan. Body mendukung teks kaya (tebal, tautan, daftar, kutipan).')
                                    ->icon('heroicon-m-document-text')
                                    ->schema([
                                        TextInput::make('about_heading')
                                            ->label('Judul About')
                                            ->placeholder('A studio built like a product team.')
                                            ->prefixIcon('heroicon-m-bookmark')
                                            ->columnSpanFull(),
                                        RichEditor::make('about_body')
                                            ->label('Isi About')
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('site/about')
                                            ->fileAttachmentsVisibility('public')
                                            ->helperText('Editor teks kaya — paragraf, tebal/miring, tautan, daftar, kutipan.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── CONTACT + SOCIAL (merged) ──
                        Tab::make('Kontak & Sosial')
                            ->icon('heroicon-o-at-symbol')
                            ->schema([
                                Section::make('Kontak')
                                    ->description('Detail kontak yang tampil di footer & halaman kontak.')
                                    ->icon('heroicon-m-envelope')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('contact_email')
                                            ->label('Email')
                                            ->email()
                                            ->prefixIcon('heroicon-m-envelope'),
                                        TextInput::make('contact_phone')
                                            ->label('Telepon')
                                            ->tel()
                                            ->prefixIcon('heroicon-m-phone'),
                                        TextInput::make('contact_address')
                                            ->label('Alamat')
                                            ->prefixIcon('heroicon-m-map-pin')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Sosial media')
                                    ->description('Pilih platform & isi URL profil. Tarik untuk mengurutkan.')
                                    ->icon('heroicon-m-share')
                                    ->schema([
                                        Repeater::make('social_links')
                                            ->hiddenLabel()
                                            ->schema([
                                                Select::make('platform')
                                                    ->required()
                                                    ->native(false)
                                                    ->options([
                                                        'X' => 'X (Twitter)', 'LinkedIn' => 'LinkedIn', 'GitHub' => 'GitHub',
                                                        'Instagram' => 'Instagram', 'Dribbble' => 'Dribbble', 'Behance' => 'Behance',
                                                        'YouTube' => 'YouTube', 'Facebook' => 'Facebook', 'TikTok' => 'TikTok',
                                                        'Threads' => 'Threads', 'WhatsApp' => 'WhatsApp', 'Email' => 'Email',
                                                    ]),
                                                TextInput::make('url')
                                                    ->required()
                                                    ->url()
                                                    ->prefixIcon('heroicon-m-link')
                                                    ->placeholder('https://...'),
                                            ])
                                            ->columns(2)
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                                            ->addActionLabel('Tambah sosial media'),
                                    ]),
                            ]),

                        // ── STATS (split out from social) ──
                        Tab::make('Statistik')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Angka highlight')
                                    ->description('Statistik yang ditonjolkan di situs (mis. "120+ proyek").')
                                    ->icon('heroicon-m-chart-bar')
                                    ->schema([
                                        Repeater::make('stats')
                                            ->hiddenLabel()
                                            ->schema([
                                                TextInput::make('value')
                                                    ->label('Angka')
                                                    ->required()
                                                    ->placeholder('120+')
                                                    ->prefixIcon('heroicon-m-hashtag'),
                                                TextInput::make('label')
                                                    ->label('Keterangan')
                                                    ->required()
                                                    ->placeholder('Proyek selesai'),
                                            ])
                                            ->columns(2)
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                            ->addActionLabel('Tambah stat'),
                                    ]),
                            ]),

                        // ── SEO + ANALYTICS ──
                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Metadata')
                                    ->description('Judul, deskripsi & kata kunci untuk mesin pencari dan pratinjau sosial.')
                                    ->icon('heroicon-m-document-magnifying-glass')
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('Judul SEO')
                                            ->maxLength(60)
                                            ->prefixIcon('heroicon-m-hashtag')
                                            ->helperText('±50–60 karakter. Kosong = pakai nama brand.'),
                                        Textarea::make('seo_description')
                                            ->label('Deskripsi meta')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('±150–160 karakter. Tampil di hasil pencarian.')
                                            ->columnSpanFull(),
                                        TextInput::make('seo_keywords')
                                            ->label('Kata kunci')
                                            ->placeholder('product studio, saas, web design')
                                            ->prefixIcon('heroicon-m-key')
                                            ->helperText('Pisahkan dengan koma.')
                                            ->columnSpanFull(),
                                        FileUpload::make('seo_image_path')
                                            ->label('Gambar berbagi sosial (OG)')
                                            ->image()
                                            ->disk('public')
                                            ->directory('site/seo')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                                            ->helperText('Disarankan 1200×630px, maks 2 MB.')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Analytics & pengindeksan')
                                    ->description('Hubungkan Google Analytics dan atur perilaku indeks mesin pencari.')
                                    ->icon('heroicon-m-chart-pie')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('google_analytics_id')
                                            ->label('Google Analytics ID (GA4)')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->prefixIcon('heroicon-m-presentation-chart-line')
                                            ->helperText('Measurement ID GA4. Kosongkan untuk menonaktifkan pelacakan.')
                                            ->rule('regex:/^(G|UA|GT|AW)-?[A-Z0-9\-]+$/i')
                                            ->validationMessages(['regex' => 'Format ID tidak valid (contoh: G-XXXXXXXXXX).']),
                                        Toggle::make('seo_noindex')
                                            ->label('Sembunyikan dari mesin pencari (noindex)')
                                            ->helperText('Aktifkan untuk situs staging/privat. Saat aktif, mesin pencari tidak mengindeks situs.')
                                            ->inline(false),
                                    ]),
                            ]),

                        // ── FOOTER: every footer field lives here, nowhere else ──
                        Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                Section::make('Ajakan (CTA) footer')
                                    ->description('Pita ajakan besar di atas footer.')
                                    ->icon('heroicon-m-rocket-launch')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('footer_cta_eyebrow')
                                            ->label('Eyebrow')
                                            ->placeholder("Let's build")
                                            ->prefixIcon('heroicon-m-tag')
                                            ->columnSpanFull(),
                                        Textarea::make('footer_cta_title')
                                            ->label('Judul')
                                            ->placeholder("Have something\nworth building?")
                                            ->helperText('Gunakan baris baru untuk memecah judul menjadi dua baris.')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('footer_cta_body')
                                            ->label('Deskripsi')
                                            ->placeholder("Tell us where you're headed. We'll tell you the shortest honest path to get there.")
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('footer_cta_label')
                                            ->label('Teks tombol')
                                            ->placeholder('Start a project')
                                            ->prefixIcon('heroicon-m-cursor-arrow-rays'),
                                        TextInput::make('footer_cta_url')
                                            ->label('URL tombol')
                                            ->placeholder('/start')
                                            ->prefixIcon('heroicon-m-link')
                                            ->rule('regex:/^(https?:\/\/|\/|#|mailto:|tel:)/i')
                                            ->validationMessages(['regex' => 'Gunakan path relatif (/...) atau URL http(s)://, mailto:, tel:.']),
                                    ]),
                                Section::make('Identitas footer')
                                    ->description('Tagline, lokasi, copyright & watermark — semua khusus footer.')
                                    ->icon('heroicon-m-bars-3-bottom-left')
                                    ->columns(2)
                                    ->schema([
                                        RichEditor::make('footer_tagline')
                                            ->label('Tagline')
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('site/footer')
                                            ->fileAttachmentsVisibility('public')
                                            ->helperText('Teks kaya di bawah logo footer (tebal, tautan, dll.).')
                                            ->columnSpanFull(),
                                        TextInput::make('footer_location')
                                            ->label('Lokasi')
                                            ->placeholder('Jakarta · Remote-first')
                                            ->prefixIcon('heroicon-m-map-pin'),
                                        TextInput::make('footer_copyright')
                                            ->label('Nama copyright')
                                            ->placeholder('Creative Trees Group')
                                            ->helperText('Tahun ditambahkan otomatis: © '.date('Y').' + teks ini. Kosong = pakai nama brand.')
                                            ->prefixIcon('heroicon-m-calendar'),
                                        TextInput::make('footer_watermark')
                                            ->label('Teks watermark (opsional)')
                                            ->placeholder('Creative Trees Group')
                                            ->helperText('Teks besar di latar footer. Kosong = pakai nama brand.')
                                            ->prefixIcon('heroicon-m-sparkles')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
