# Deployment & Operations

Production runbook for the Creative Trees CMS (Laravel + Filament, PostgreSQL).

## 1. First-time deploy

```bash
# 1. Install
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 2. Environment
cp .env.example .env          # then edit — see §2
php artisan key:generate      # sets APP_KEY (encrypts CMS mail passwords — keep it stable!)

# 3. Database
php artisan migrate --force
php artisan db:seed --force   # seeds admin, site content, and the role-based mailboxes

# 4. Permissions (Shield RBAC) — REQUIRED, and after every new page/widget/resource
php artisan shield:generate --all --panel=admin

# 5. Storage + caches
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Then sign in at `/admin` and assign roles under **Roles** (see §4).

## 2. Environment (`.env`) essentials

| Key | Notes |
|-----|-------|
| `APP_KEY` | Set once via `key:generate`. **Encrypts the CMS-stored SMTP passwords** — changing it makes them undecryptable. |
| `DB_*` | PostgreSQL connection. |
| `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` | The seeded admin. In production the admin is only created when `ADMIN_SEED_PASSWORD` is set. Username is derived from the email local-part. |
| `PANEL_MFA_REQUIRED` | `false` by default (enrol first, then enforce). Set `true` **only after** a factor is enrolled, or the admin is locked on the setup screen. |
| `MAIL_MAILER` / `MAIL_HOST` / `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` | Default transactional sender. |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Default From. |
| `MAIL_<ROLE>_PASSWORD` | Optional per-account SMTP password fallback (e.g. `MAIL_NO_REPLY_PASSWORD`, `MAIL_SUPPORT_PASSWORD`). Only used when the account has no CMS-stored password. |

Secrets (`APP_KEY`, DB, `*_PASSWORD`) live **only** in `.env` on the server — never commit them.

## 3. Email / SMTP accounts

Managed in **Site Settings → Contact & Social → Email addresses**:

- Non-secret transport per account (mailer, host, port, encryption SSL/TLS, username) is stored in the DB.
- The **password** is stored **encrypted (AES-256 via `APP_KEY`)** and is write-only in the UI (never echoed back).
- Use each account row's **"Send test"** button to verify SMTP works.
- Lead notifications send via the **`no-reply`** account when configured, else the default `.env` mailer.

Alternatively, set a per-account password in `.env` as `MAIL_<ROLE>_PASSWORD` (fallback).

## 4. Access control (Shield RBAC)

- `/admin` access requires the user to have **at least one role** (`canAccessPanel()` → `roles()->exists()`).
- The **`developer`** role bypasses every permission (global `Gate::before`) — the standard super-admin.
- Every resource, page, and widget is gated by a Shield permission (`View:*`, `Create:*`, …). Manage them under **Roles**.
- Grant a non-developer role **`View:Dashboard`** so it has a landing page, plus the `View:<Widget>` / resource permissions it should see.
- After adding any new resource/page/widget, re-run `php artisan shield:generate --all --panel=admin` (the `ShieldCoverageTest` will fail if an entity is un-gated).

## 5. Routine deploy (updates)

```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan shield:generate --all --panel=admin     # if entities changed
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:restart                            # if using queued workers
```

## 6. Maintenance mode

Toggle **Site Settings → System → Maintenance mode** (instant, no CLI). Admins and signed-in users keep access; public visitors see the maintenance page.

## 7. Verify a deploy

```bash
php artisan test          # full suite must be green
php artisan about         # confirm env, cache, DB connection
```

## Break-glass

- **Locked out by MFA:** set `PANEL_MFA_REQUIRED=false` in `.env`, then `php artisan config:clear`.
- **Lost admin role:** `php artisan shield:super-admin` (assigns the super-admin/developer role to a user).
