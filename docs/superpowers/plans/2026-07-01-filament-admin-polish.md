# Filament Admin Polish — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring every CMS resource in the Filament 5.6 admin panel to the quality of `SiteSettingForm`, refactor Edit Lead to tabs, and add a read-only View page + infolist to every resource.

**Architecture:** Per-resource Form/Table/Infolist classes following one shared "house style" convention list. New `View<Model>` ViewRecord page + `<Model>Infolist` schema per resource; resources register the view route and override `infolist()`.

**Tech Stack:** Laravel 13, Filament 5.6, PHP 8.3. Layout from `Filament\Schemas\Components\*`; form fields from `Filament\Forms\Components\*`; infolist entries from `Filament\Infolists\Components\*`; table from `Filament\Tables\*`; `Filament\Resources\Pages\ViewRecord`.

## Global Constraints

- UI language: **English** everywhere (rename Indonesian section/tab titles).
- No DB migrations. Media columns keep storing string paths; `socials` stays `array` cast.
- Do not touch: navigation groups, `Showcase`/`Pricing` nav items, `SiteSettings`, `Users`, `SiteContents` (retired).
- `sort` field is **hidden from forms**; ordering via table `reorderable('sort')`.
- Every form field: `prefixIcon` + `helperText` + (free-text) `placeholder`.
- Every table: `defaultSort` + `reorderable('sort')` (where a sort col exists) + relevant filters + `emptyState*` + row `ActionGroup([ViewAction, EditAction])` + keep `DeleteBulkAction`.
- Every resource: add `recordTitleAttribute`, `infolist()`, and `'view'` page.
- FileUpload canonical: `->image()->imageEditor()->disk('public')->directory('site/<area>')->visibility('public')->maxSize(2048)->acceptedFileTypes(['image/png','image/jpeg','image/webp','image/svg+xml'])`.

---

## Reference templates (canonical code — reused verbatim)

### T-A. View page (`Pages/View<Model>.php`)

```php
<?php

namespace App\Filament\Resources\<Plural>\Pages;

use App\Filament\Resources\<Plural>\<Model>Resource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class View<Model> extends ViewRecord
{
    protected static string $resource = <Model>Resource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
```

### T-B. Resource wiring (add to each `<Model>Resource.php`)

```php
use App\Filament\Resources\<Plural>\Schemas\<Model>Infolist;
use Filament\Schemas\Schema;

protected static ?string $recordTitleAttribute = '<title|name|question>';

public static function infolist(Schema $schema): Schema
{
    return <Model>Infolist::configure($schema);
}

// in getPages():
'view' => Pages\View<Model>::route('/{record}'),
```

### T-C. Table additions

```php
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

return $table
    ->defaultSort('sort')                 // or ->defaultSort('created_at','desc')
    ->reorderable('sort')                 // only where a sort column exists
    ->columns([ /* existing + ImageColumn for media */ ])
    ->filters([ /* see per-resource */ ])
    ->recordActions([
        ActionGroup::make([ViewAction::make(), EditAction::make()]),
    ])
    ->toolbarActions([
        BulkActionGroup::make([DeleteBulkAction::make()]),
    ])
    ->emptyStateIcon('<heroicon-o-…>')
    ->emptyStateHeading('No <plural> yet')
    ->emptyStateDescription('<one helpful sentence>.')
    ->emptyStateActions([CreateAction::make()]);
```

`ImageColumn` (`Filament\Tables\Columns\ImageColumn`): `ImageColumn::make('cover_path')->label('Cover')->disk('public')->height(40)->square()` (use `->circular()` for avatars).

`TernaryFilter`:
```php
TernaryFilter::make('is_featured')->label('Featured')->placeholder('All')
    ->trueLabel('Featured only')->falseLabel('Not featured'),
```

`SelectFilter` (relationship):
```php
SelectFilter::make('category')->relationship('category','name')->searchable()->preload(),
```

### T-D. Infolist (`Schemas/<Model>Infolist.php`)

```php
<?php

namespace App\Filament\Resources\<Plural>\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class <Model>Infolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('<Group>')->icon('<heroicon-m-…>')->columns(2)->schema([
                TextEntry::make('<field>')->icon('<heroicon-m-…>'),
                // ImageEntry::make('cover_path')->disk('public') for media
                // IconEntry::make('is_featured')->boolean() for flags
                // TextEntry::make('status')->badge() for status
                // KeyValueEntry::make('meta') / TextEntry::make('items')->badge() for arrays
            ]),
        ]);
    }
}
```

### T-E. Form field convention (per field)

```php
TextInput::make('title')->required()->prefixIcon('heroicon-m-bars-3-bottom-left')
    ->placeholder('Acme rebrand')->helperText('Shown as the heading on the public page.');

FileUpload::make('cover_path')->label('Cover image')
    ->image()->imageEditor()->disk('public')->directory('site/projects')->visibility('public')
    ->maxSize(2048)->acceptedFileTypes(['image/png','image/jpeg','image/webp','image/svg+xml'])
    ->helperText('Main image. PNG/JPG/WEBP, max 2 MB.');

TagsInput::make('services')->placeholder('Add a service')->helperText('Press Enter after each item.');

KeyValue::make('socials')->keyLabel('Platform')->valueLabel('URL')->helperText('e.g. LinkedIn → https://…');
```

---

## Task 0: Lead — tabs form + table + infolist + view (REFERENCE, full code)

**Files:** modify `Leads/Schemas/LeadForm.php`, `Leads/Tables/LeadsTable.php`, `Leads/LeadResource.php`; create `Leads/Schemas/LeadInfolist.php`, `Leads/Pages/ViewLead.php`.

- [ ] **Step 1 — LeadForm with Tabs**

```php
<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Lead')->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Contact')->icon('heroicon-o-user')->columns(2)->schema([
                    TextInput::make('name')->required()->prefixIcon('heroicon-m-user')
                        ->placeholder('Jane Doe')->helperText('Full name of the person enquiring.'),
                    TextInput::make('email')->label('Email address')->email()->required()
                        ->prefixIcon('heroicon-m-envelope')->placeholder('jane@company.com')
                        ->helperText('We reply to this address.'),
                    TextInput::make('company')->prefixIcon('heroicon-m-building-office-2')
                        ->placeholder('Acme Inc.')->helperText('Optional — their organisation.'),
                    TextInput::make('phone')->tel()->prefixIcon('heroicon-m-phone')
                        ->placeholder('+62 812 3456 7890')->helperText('Optional contact number.'),
                ]),
                Tab::make('Inquiry')->icon('heroicon-o-chat-bubble-left-right')->columns(2)->schema([
                    TextInput::make('budget')->prefixIcon('heroicon-m-banknotes')
                        ->placeholder('$50k+')->helperText('Stated budget range, if any.'),
                    TextInput::make('service_interest')->label('Service interest')
                        ->prefixIcon('heroicon-m-squares-2x2')->placeholder('UX & UI Design')
                        ->helperText('What they are interested in.'),
                    Textarea::make('message')->required()->rows(6)->columnSpanFull()
                        ->placeholder('Tell us about the project…')
                        ->helperText('The enquiry message submitted from the site.'),
                ]),
                Tab::make('Internal')->icon('heroicon-o-cog-6-tooth')->columns(2)->schema([
                    Select::make('status')->required()->native(false)->default('new')
                        ->prefixIcon('heroicon-m-flag')
                        ->options(collect(Lead::STATUSES)->mapWithKeys(fn ($s) => [$s => Str::headline($s)])->all())
                        ->helperText('Pipeline stage for this lead.'),
                    TextInput::make('source')->default('website')->disabled()->dehydrated()
                        ->prefixIcon('heroicon-m-globe-alt')->helperText('Where the lead came from.'),
                    KeyValue::make('meta')->columnSpanFull()->keyLabel('Key')->valueLabel('Value')
                        ->helperText('Extra metadata captured with the submission (UTM, IP, …).'),
                    Placeholder::make('created_at')->label('Received')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '—'),
                    Placeholder::make('updated_at')->label('Last updated')
                        ->content(fn ($record) => $record?->updated_at?->diffForHumans() ?? '—'),
                ]),
            ]),
        ]);
    }
}
```

- [ ] **Step 2 — LeadsTable** (status filter kept; add date filter, emptyState, ActionGroup, toggleable company/phone) — full code in spec §4.2; key additions: `Filter::make('created_at')` with `DatePicker` from/until + `->query()` whereDate; `ActionGroup([ViewAction, EditAction])`; `emptyStateIcon('heroicon-o-inbox')` + heading/description (Lead keeps no `emptyStateActions` create OR includes `CreateAction` — CreateLead page exists, so include it).
- [ ] **Step 3 — LeadInfolist**: Sections Contact (name/email copyable/company/phone) / Inquiry (budget/service_interest badge/message prose/status badge+color/source) / Metadata collapsed (created_at since/updated_at since/KeyValueEntry meta).
- [ ] **Step 4 — ViewLead** (template T-A; Model=Lead, Plural=Leads).
- [ ] **Step 5 — LeadResource** (T-B; `recordTitleAttribute='name'`).
- [ ] **Step 6 — Verify**: `php artisan filament:cache-components && php artisan optimize:clear && php artisan about > /dev/null && echo OK`; open Leads list/view/edit.
- [ ] **Step 7 — Commit** `git commit -m "feat(admin): Lead tabs form + view page + infolist + table filters"`

---

## Task 1: Faqs — REFERENCE for simple resources (full code)

- [ ] **Step 1 — FaqForm**: Section "FAQ" (icon `question-mark-circle`, description) → `question` TextInput (required, icon, placeholder, helper), `answer` Textarea (required, rows 4, full width, placeholder, helper), `is_published` Toggle (default true, inline(false), helper). `sort` removed from form.
- [ ] **Step 2 — FaqsTable**: `defaultSort('sort')`, `reorderable('sort')`, columns question(searchable, wrap)/answer(limit 60, toggleable, gray)/is_published IconColumn boolean/sort+created_at toggleable hidden; filter `TernaryFilter('is_published')`; ActionGroup(View,Edit); emptyState icon `heroicon-o-question-mark-circle` + CreateAction.
- [ ] **Step 3 — FaqInfolist**: Section FAQ → question(bold), answer(prose, full width), is_published boolean.
- [ ] **Step 4 — ViewFaq** (T-A; Model=Faq, Plural=Faqs).
- [ ] **Step 5 — FaqResource** (T-B; `recordTitleAttribute='question'`).
- [ ] **Step 6 — Verify** list/view/edit render; reorder drag persists.
- [ ] **Step 7 — Commit** `git commit -m "feat(admin): Faqs polish + view/infolist (simple-resource reference)"`

---

## Tasks 2–14: remaining resources (apply T-A…T-E with these concrete specs)

> Each task = 5 file ops (Form, Table, Infolist, View page, Resource wiring) + verify + commit.
> Default: single `Section` (icon + description, 2 cols), every field iconed+helper+placeholder; `sort` hidden; table gets `defaultSort('sort')` + `reorderable('sort')` + emptyState + ActionGroup; infolist mirrors columns.

### Task 2 — Categories (`recordTitleAttribute='name'`)
- Form: `name` (icon `tag`), `slug` (icon `link`, helper "URL segment — lowercase, no spaces"), `type` (icon `rectangle-stack`, helper "Groups projects/products"), `description` → **Textarea**.
- Table: name/slug/description/type/sort/timestamps; Filter `SelectFilter::make('type')->options(Category::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type','type')->all())`; emptyState icon `heroicon-o-tag`.
- Infolist: name, slug, type(badge), description.

### Task 3 — Principles (`recordTitleAttribute='title'`)
- Form: `title` (icon `light-bulb`), `description` → Textarea full width.
- Table: title/description/sort/timestamps; reorder; emptyState `heroicon-o-light-bulb`.
- Infolist: title(bold), description(prose).

### Task 4 — StartSteps (`recordTitleAttribute='title'`)
- Same shape as Principles; icon `list-bullet`; emptyState `heroicon-o-list-bullet`.

### Task 5 — PricingIncludes (`recordTitleAttribute='label'`)
- Form: `label` (icon `check-circle`), `description` → Textarea.
- Table/Infolist: label/description; emptyState `heroicon-o-check-circle`.

### Task 6 — NavLinks (`recordTitleAttribute='label'`)
- Form: `location` Select (header/footer_studio/footer_company; icon `map-pin`, helper), `label` (icon `bookmark`), `url` (icon `link`, placeholder `/work`, helper).
- Table: keep `location` badge; switch `defaultSort('location')`→keep + add `reorderable('sort')`; Filter `SelectFilter::make('location')->options(['header'=>'Header','footer_studio'=>'Footer · Studio','footer_company'=>'Footer · Company'])`; emptyState `heroicon-o-link`.
- Infolist: location(badge), label, url(copyable, `->url(fn($r)=>$r->url)`).

### Task 7 — ProcessPhases (`recordTitleAttribute='name'`)
- Form: `name` (icon `rectangle-group`), `lead` Textarea required (helper "One-line summary"), `body` Textarea (helper "Full description"), `deliverables` TagsInput (helper).
- Table: add `TextColumn::make('deliverables')->badge()`; keep name/lead/sort; reorder; emptyState `heroicon-o-rectangle-group`.
- Infolist: name, lead, body(prose full width), deliverables(badge).

### Task 8 — Services (`recordTitleAttribute='title'`)
- Form: `title` (icon `swatch`), `slug` (icon `link`), `icon` (icon `sparkles`, helper "Heroicon name, e.g. heroicon-o-cube"), `summary` Textarea, `description` Textarea, `capabilities` → **TagsInput**, `is_featured` Toggle.
- Table: title/slug/icon/summary + is_featured IconColumn + sort; Filter `TernaryFilter('is_featured')`; reorder; emptyState `heroicon-o-swatch`.
- Infolist: title, slug, icon, summary, description(prose), capabilities(badges), is_featured boolean.

### Task 9 — TeamMembers (`recordTitleAttribute='name'`)
- Form (2 cols): `name` (icon `user`), `slug` (icon `link`), `role` (icon `briefcase`), `bio` Textarea full width, `photo_path` → **FileUpload** (directory `site/team`, `->avatar()` or `->image()->imageEditor()`, helper), `socials` → **KeyValue** (keyLabel Platform, valueLabel URL, helper), `is_published` Toggle.
- Table: `ImageColumn::make('photo_path')->circular()->disk('public')`; name/slug/role + is_published IconColumn + sort; Filter `TernaryFilter('is_published')`; reorder; emptyState `heroicon-o-user-group`.
- Infolist: ImageEntry photo(circular), name, role, slug, bio(prose), KeyValueEntry socials, is_published boolean.

### Task 10 — Clients (`recordTitleAttribute='name'`)
- Form: keep; add Section `->description('Logos shown in the "trusted by" strip.')`; ensure helper/placeholder on all; `sort` hidden.
- Table: keep ImageColumn + reorder + `defaultSort('sort')`; Filter `TernaryFilter('is_featured')`; emptyState `heroicon-o-building-office-2`; ActionGroup(View,Edit).
- Infolist: ImageEntry logo, name, website_url(link), is_featured boolean.

### Task 11 — PricingTiers (`recordTitleAttribute='name'`) — keep Tabs
- Form (Tabs → English Detail/Pricing/Content/Settings): helper+placeholder every field; `items` TagsInput; `sort` hidden.
- Table: name/term/price + is_featured IconColumn + sort; Filter `TernaryFilter('is_featured')`; reorder; emptyState `heroicon-o-banknotes`.
- Infolist: Detail(name, term, tagline) / Pricing(price_label, price, suffix) / Content(items badges) / Settings(is_featured).

### Task 12 — Testimonials (`recordTitleAttribute='author'`) — keep Tabs
- Form (Tabs → English Detail/Content/Settings): helper+placeholder; `avatar_path` → **FileUpload** (directory `site/testimonials`); `project_id` Select; `rating` numeric (helper "1–5"); `sort` hidden.
- Table: `ImageColumn::make('avatar_path')->circular()->disk('public')`; project.title/author/role/company/rating + is_featured IconColumn; Filters `SelectFilter::make('project')->relationship('project','title')`, `TernaryFilter('is_featured')`; reorder; emptyState `heroicon-o-chat-bubble-left-right`.
- Infolist: Detail(project, author, role, company) / Content(quote prose, rating) / Settings(avatar ImageEntry, is_featured).

### Task 13 — Products (`recordTitleAttribute='title'`) — keep Tabs
- Form (Tabs → English Detail/Content/Media/Settings): helper+placeholder; `cover_path` → **FileUpload** (Media, directory `site/products`); `features` → **TagsInput** (Content); `category_id` Select; `sort` hidden.
- Table: add `ImageColumn::make('cover_path')`; keep category.name/title/slug/type/price_label/status badge/is_featured; Filters `SelectFilter::make('category')->relationship('category','name')`, `SelectFilter::make('status')->options(Product::query()->whereNotNull('status')->distinct()->pluck('status','status')->all())`, `SelectFilter::make('type')->options(...distinct type...)`, `TernaryFilter('is_featured')`; reorder; emptyState `heroicon-o-cube`.
- Infolist: Detail(category, title, slug, type, status badge) / Content(summary, description prose, features badges, price_label) / Media(cover ImageEntry) / Links(cta_label, cta_url) / Settings(is_featured).

### Task 14 — Projects (`recordTitleAttribute='title'`) — keep Tabs
- Form (Tabs → English Detail/Content/Media/Settings): helper+placeholder; Media: `cover_path` FileUpload (directory `site/projects`), `gallery` FileUpload `->multiple()->reorderable()` (directory `site/projects/gallery`), `website_url` (icon link); Content: `summary` Textarea, `body` Textarea, `services`+`results` TagsInput; Detail: `category_id` Select, `title`, `slug`, `client_name`, `year` numeric, `role`; Settings: `is_featured` Toggle, `status`, `sort` hidden.
- Table: add `ImageColumn::make('cover_path')`; keep rich columns; Filters `SelectFilter::make('category')->relationship('category','name')`, `SelectFilter::make('status')->options(...distinct...)`, `TernaryFilter('is_featured')`; reorder; emptyState `heroicon-o-briefcase`.
- Infolist: Detail(category, title, slug, client_name, year, role) / Content(summary, body prose, services badges, results badges) / Media(cover ImageEntry, gallery ImageEntry, website_url link) / Settings(is_featured, status badge).

Each task ends with: `php artisan optimize:clear` then `git add app/Filament/Resources/<Plural> && git commit -m "feat(admin): <Resource> polish + view/infolist"`.

---

## Final verification
- [ ] `php artisan filament:cache-components && php artisan optimize:clear` — no errors.
- [ ] Load every List page — filters work, empty states show, reorder drags.
- [ ] Open one record per resource — View (infolist) + Edit (FileUpload/TagsInput/KeyValue round-trip).
- [ ] `php artisan about` exits 0.
- [ ] `grep -rin "Pengaturan\|Konten\|Kategori\|Layanan\|Anggota\|Prinsip\|Langkah\|Tautan\|Termasuk\|Klien\|Harga" app/Filament/Resources` returns only intentional English.

## Self-review notes
- Spec §3.1–3.3 → Tasks 0–14 cover every resource + field conversion.
- Spec §4 (Lead) → Task 0 (full code).
- Spec §5 matrix → Tasks 2–14 one-to-one (14 CMS resources + Lead = 15).
- Reference code complete (T-A…T-E + Task 0/1); per-resource tasks specify exact fields, icons, filters, template.
