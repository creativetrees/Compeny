# Creative Trees — Professional Real-time Admin Dashboard

**Date:** 2026-07-01
**Status:** Approved (design) → implementation
**Panel:** Filament v5.6 (native) + `leandrocfe/filament-apex-charts`

## Goal

Replace the minimal `StudioStats` widget with a complete, real-time, brand-aligned
executive dashboard for the Creative Trees private company CMS. All widgets are
native Filament where possible; rich charts use the ApexCharts plugin. Labels in
English. Live updates via Livewire polling at **5s**.

## Data model (existing)

- `Lead`: `status` ∈ new|contacted|qualified|won|lost, `source`, `service_interest`,
  `budget` (free text), `company`, `created_at`. STATUSES const on model.
- `Project` (`scopePublished` = status 'published'), `Client`, `Testimonial`,
  `Service`, `TeamMember` — all have factories.
- DB currently empty (1 user) → demo seeder required.

## Components

### 1. Plugin
- `composer require leandrocfe/filament-apex-charts` (Filament v5-compatible release).
- Fallback if no v5 release: native `Filament\Widgets\ChartWidget` (Chart.js).

### 2. Custom Dashboard page — `app/Filament/Pages/Dashboard.php`
- Extends `Filament\Pages\Dashboard`, uses `HasFiltersForm`.
- Period filter: Last 7 / 30 / 90 days / This year / All time (default 30 days).
- Optional explicit start/end date pickers.
- Widgets read the active period via `InteractsWithPageFilters`.

### 3. Widgets (`app/Filament/Widgets/`)

| Widget | Base | Content |
|---|---|---|
| `StudioOverview` | `StatsOverviewWidget` | KPIs w/ sparkline + ±% trend vs previous period: Total Leads, New (awaiting), Qualified, Won, Conversion Rate %, Content health (published projects). |
| `LeadsOverTimeChart` | ApexChartWidget (area) | Leads/day for the period; 2 series = current vs previous period. |
| `LeadsByStatusChart` | ApexChartWidget (donut) | Count per status, semantic colors. |
| `LeadsBySourceChart` | ApexChartWidget (horizontal bar) | Top lead sources. |
| `ConversionFunnelChart` | ApexChartWidget (bar funnel) | new → contacted → qualified → won drop-off. |
| `TopServicesChart` | ApexChartWidget (bar) | Most-requested `service_interest`. |
| `LatestLeads` | `TableWidget` | 10 newest leads: name, company, service, status badge, source, "x ago"; row → LeadResource edit. |

All widgets: `$pollingInterval = '5s'`, brand theme (zinc + accent `#f97316`),
number formatting, tooltips, responsive, graceful empty state.

### 4. Layout (12-col grid, full width)
- Row 1: `StudioOverview` (12)
- Row 2: `LeadsOverTimeChart` (8) + `LeadsByStatusChart` (4)
- Row 3: `LeadsBySourceChart` (6) + `ConversionFunnelChart` (6)
- Row 4: `TopServicesChart` (6) + `LatestLeads` (6) — or `LatestLeads` full width
- Order/width via `$sort` + `$columnSpan`.

### 5. Demo data — `database/seeders/DemoDashboardSeeder.php`
- ~150 leads spread Jan–Jul 2026, weighted statuses (funnel-realistic: many new,
  few won), varied source (website/referral/instagram/ads/linkedin),
  realistic service_interest/budget, randomized `created_at` per day.
- Seed Projects/Clients/Testimonials/Services via factories if empty.
- Idempotent guard (skip if leads already > threshold). Separate from
  `DatabaseSeeder`. Run: `php artisan db:seed --class=DemoDashboardSeeder`.

### 6. Registration
- Remove old `StudioStats`; widgets auto-discovered. Order via `$sort`.
- Keep `AccountWidget`/`FilamentInfoWidget` last (or drop from dashboard).

## Out of scope
- No new DB columns/migrations. No changes to the public lead form.
- Pipeline $ value omitted (budget is free-text ranges, not summable reliably).

## Verification
- `composer`/`artisan` run clean; dashboard renders all widgets populated after seeding.
- Polling refreshes numbers without full reload.
- Period filter changes update every widget.
