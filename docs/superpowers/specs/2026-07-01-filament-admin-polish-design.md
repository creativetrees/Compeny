# Filament Admin Polish — Design Spec

**Date:** 2026-07-01
**Status:** Approved direction (pending spec review)
**Scope:** Bring every CMS resource in the Filament v5.6 admin panel up to the
quality bar set by `SiteSettingForm` (the "gold standard"), and refactor the
**Edit Lead** screen to use tabs. Stack: Laravel 13, Filament 5.6, PHP 8.3.

---

## 1. Goals

1. **Lead** edit screen uses **Tabs** (Contact · Inquiry · Internal) — professional,
   icon/helper-rich, consistent with Site Settings.
2. Every CMS resource form gets the house style: prefix icons, helper text,
   placeholders, sectioned/tabbed layout, proper input components for media &
   array fields.
3. Every CMS resource table gets: a default sort, drag-and-drop reordering,
   relevant filters, image columns for media, and an empty state.
4. Every CMS resource gets a **read-only View page** backed by an **infolist**.
5. The whole admin UI reads in **English** (consistent with Site Settings).

### Non-goals
- No DB schema/migration changes. Media columns keep storing a string path
  (FileUpload is compatible); `socials` stays an `array` cast (KeyValue is
  compatible).
- No change to navigation groups or the synthetic `Showcase`/`Pricing` parent
  items — these are already correct in `AdminPanelProvider::navigationItems()`.
- `SiteContents` is intentionally retired from navigation
  (`shouldRegisterNavigation: false`) — left untouched.
- `SiteSettings` and `Users` resources are out of scope (already polished / not
  in the Image #2 sidebar request).

---

## 2. Decisions (from brainstorming)

| Decision | Choice |
|---|---|
| Lead form layout | **Tabs** (Contact · Inquiry · Internal) |
| UI language | **Full English** everywhere (rename Indonesian section/tab titles) |
| View pages | **Add** `ViewRecord` page + infolist schema per resource |
| Ordering | **Drag reorder** via `reorderable('sort')`; hide the numeric `sort` form field |

---

## 3. House style — shared conventions

These conventions are applied per-resource (Filament idiom keeps each
Form/Table/Infolist self-contained). No heavy shared abstraction; only obvious,
local consistency.

### 3.1 Forms
- **Layout:** resources with media + content + settings use top-level `Tabs`
  (`Detail` · `Content` · `Media` · `Settings`, each with an icon). Simple
  resources use one or two `Section`s with an icon + a one-line `description`.
- **Every field:** `prefixIcon` (role-appropriate), a concise `helperText`
  (what it controls / where it appears on the public site), and a realistic
  `placeholder` for free-text inputs.
- **Media path fields → `FileUpload`** (`->image()->imageEditor()->disk('public')
  ->directory('site/<area>')->visibility('public')`, sensible `maxSize` +
  `acceptedFileTypes`):
  - `Projects.cover_path`, `Projects.gallery` (`->multiple()->reorderable()`)
  - `Products.cover_path`
  - `Testimonials.avatar_path`
  - `TeamMembers.photo_path`
  - (`Clients.logo_path` already done — used as the reference.)
- **Array fields → typed inputs:**
  - `Projects.services`, `Projects.results`, `Products.features`,
    `Services.capabilities` → `TagsInput`
  - `TeamMembers.socials` → `KeyValue` (platform → URL)
  - (`PricingTiers.items`, `ProcessPhases.deliverables` already `TagsInput`.)
- **Long copy:** plain `TextInput` holding paragraph copy → `Textarea`
  (e.g. `Categories.description`).
- **`sort` field:** hidden from the form (ordering handled by table drag).
- **`slug` fields:** keep, but add helper ("URL segment — lowercase, no spaces").

### 3.2 Tables
- `->defaultSort('sort')` where a `sort` column exists; otherwise `created_at desc`.
- `->reorderable('sort')` on every ordered content list.
- **Filters** (in `->filters([...])`):
  - `TernaryFilter` for each `is_featured` / `is_published`.
  - `SelectFilter` (relationship/options) for: `Projects.category_id` + `status`,
    `Products.category_id` + `status` + `type`, `Categories.type`,
    `Testimonials.project_id`, `NavLinks.location`.
  - Lead: keep status `SelectFilter`; add a `created_at` date-range `Filter`.
- `ImageColumn` for media columns (`cover_path`, `avatar_path`, `photo_path`,
  `logo_path`) replacing raw path text where the column is shown.
- `emptyState`: `emptyStateIcon` + `emptyStateHeading` + `emptyStateDescription`
  + `emptyStateActions([CreateAction])`.
- Row actions: an `ActionGroup` with `ViewAction` + `EditAction`. Bulk: keep
  `DeleteBulkAction`.
- Timestamps stay `toggleable(isToggledHiddenByDefault: true)`.

### 3.3 Infolists (new) + View pages (new)
- New file per resource: `Schemas/<Model>Infolist.php` with
  `configure(Schema $schema): Schema` returning grouped `Section`s of
  `TextEntry` / `ImageEntry` / `IconEntry` (badges & colors mirror the table).
- New page per resource: `Pages/View<Model>.php` extending
  `Filament\Resources\Pages\ViewRecord`.
- Resource class: add `public static function infolist(Schema $schema): Schema`
  delegating to the Infolist class, and register
  `'view' => Pages\View<Model>::route('/{record}')` in `getPages()`.
- Add `protected static ?string $recordTitleAttribute` to each resource (e.g.
  `title` / `name` / `question`) so breadcrumbs, the view header, and global
  search read naturally.

---

## 4. Lead resource (Image #1 — primary request)

### 4.1 Form → `Tabs` (`persistTabInQueryString`)
- **Contact** (`heroicon-o-user`): `name`, `email`, `company`, `phone` — prefix
  icons (present), helper text, placeholders; 2-column grid.
- **Inquiry** (`heroicon-o-chat-bubble-left-right`): `budget`, `service_interest`,
  `message` (`Textarea`, full width, required).
- **Internal** (`heroicon-o-cog-6-tooth`): `status` (`Select`, `native(false)`,
  options from `Lead::STATUSES`), `source` (read-only, `disabled()->dehydrated()`),
  `meta` (`KeyValue`, full width), plus read-only "Received" / "Updated"
  `Placeholder`s.

### 4.2 Table
- Keep status badge (colors/icons already good). Add: `created_at` date-range
  filter (keep existing status `SelectFilter`), `emptyState`, `company`/`phone`
  `toggleable`, and an `ActionGroup` (View + Edit).

### 4.3 Infolist + View page
- Sections: **Contact** (name/email/phone/company), **Inquiry**
  (budget/service_interest/message/status badge/source), **Metadata**
  (meta key-values, received/updated). A `CreateLead` page already exists (admins
  can log a lead manually), so the empty state keeps a Create action like the
  other resources.

---

## 5. Per-resource change matrix

> Common to all: prefix icons + helper text + placeholders on form fields;
> `sort` hidden in form; `reorderable('sort')` + `emptyState` + View page +
> infolist + `recordTitleAttribute` + section titles → English.

### Group: Work
| Resource | Form-specific | Table-specific |
|---|---|---|
| **Projects** | tabs keep; `cover_path`→FileUpload, `gallery`→FileUpload multiple, `services`/`results`→TagsInput; tab descriptions | filters: category, status, featured; ImageColumn cover |
| **Clients** | already strong; add Section description + helper polish | filter: featured (reorder already present) |
| **Categories** | `description`→Textarea | filter: type |
| **Testimonials** | tabs keep; `avatar_path`→FileUpload | filters: project, featured; ImageColumn avatar |

### Group: Catalog
| Resource | Form-specific | Table-specific |
|---|---|---|
| **Products** | tabs keep; `cover_path`→FileUpload, `features`→TagsInput | filters: category, status, type, featured; ImageColumn cover |
| **PricingTiers** | tabs keep; helper/placeholder | filter: featured |
| **PricingIncludes** | section polish | (reorder + emptyState only) |

### Group: Content
| Resource | Form-specific | Table-specific |
|---|---|---|
| **Services** | `capabilities`→TagsInput | filter: featured |
| **TeamMembers** | `photo_path`→FileUpload, `socials`→KeyValue | filter: published; ImageColumn photo |
| **ProcessPhases** | helper/placeholder | show `deliverables` column |
| **Principles** | helper/placeholder | (reorder + emptyState only) |
| **Faqs** | helper/placeholder | filter: published |
| **StartSteps** | helper/placeholder | (reorder + emptyState only) |
| **NavLinks** | helper/placeholder | filter: location |

---

## 6. Files touched / created

For each of the 14 CMS resources **+ Lead** (15 total):
- **Edit** `Schemas/<Model>Form.php` (house style).
- **Edit** `Tables/<Model>Table.php` (sort, reorder, filters, image cols, emptyState, actions).
- **Create** `Schemas/<Model>Infolist.php`.
- **Create** `Pages/View<Model>.php`.
- **Edit** `<Model>Resource.php` (add `infolist()`, `getPages() 'view'`, `recordTitleAttribute`).

≈ 15 × 3 edits + 15 × 2 new files ≈ **75 file operations** — mechanical but
large; the implementation plan will batch by group and verify one resource
end-to-end before scaling.

---

## 7. Verification

- Route cache clear; load each List/Edit/View page in the admin panel (Filament)
  and confirm no exceptions.
- Confirm: tabs render on Lead; FileUpload accepts an image and the path persists;
  TagsInput round-trips the array; reorder drag updates `sort`; each filter
  narrows the list; each empty state shows; each View page renders the infolist.
- Spot-check one resource per group fully before applying to the rest.
- Existing manually-entered media paths still display (FileUpload reads the same
  string column).

## 8. Risks
- **FileUpload vs legacy paths:** values entered as raw relative paths must be
  valid `public`-disk paths to preview; otherwise they show as missing (data
  issue, not code). No data migration planned.
- **Infolist/ViewRecord API:** no infolist exists in the repo yet — build one
  resource first, verify against Filament 5.6, then replicate.
- **Volume:** 75 operations invite copy-paste drift; the plan enforces a single
  shared convention list and per-group review.
