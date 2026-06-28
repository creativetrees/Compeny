# Security & Production Hardening — Creative Trees Group

> Audit performed by a six-role virtual team (Security, Backend, DBA, Platform/DevOps, Frontend, QA) against the production-hardening directive, mapped to OWASP Top 10 / ASVS L2. **Context: single-tenant marketing site + Filament CMS — no multi-tenancy, no payments, no AI SDK.** Items that would be over-engineering for that context are marked **N/A** with reasoning.

**Overall verdict:** the baseline was already strong (`APP_DEBUG=false`, every model whitelists `$fillable`, hardened sessions, no debug tooling, fail-closed admin gate, honeypot + throttle, CSRF/origin verification, owner-scoped broadcast channels, `serializable_classes => false`). Findings were refinements, not breaches.

---

## 1. Trust boundaries

Only nginx (and, if enabled, the Reverb WSS port) face the internet. App, queue, PostgreSQL, and Redis live on an internal network with **no published host ports** in production (`compose.prod.yaml`).

```
Internet ──HTTPS──> nginx ──fastcgi──> php-fpm (app) ──> PostgreSQL (internal)
                      │                      │            Redis (internal)
                      └──WSS (optional)──> Reverb        Queue worker (internal)
```

---

## 2. Findings & status (worst-first, consolidated)

| # | Sev | Finding | Status |
|---|-----|---------|--------|
| FE1 | **High** | `field.blade.php` default border was a literal `{{ $borderClass }}` (regression) → invisible inputs | ✅ **Fixed** |
| S-M1 | Med | `trustProxies(at: '*')` lets a spoofed `X-Forwarded-For` bypass the lead throttle | ✅ **Fixed** — trusts loopback + private ranges, `TRUSTED_PROXIES` override |
| S-M2 / FE2 | Med | No CSP | ✅ **Partial** — `frame-ancestors`/`base-uri`/`object-src` enforced; full strict policy shipped **Report-Only** on public routes. Enforcing strict `script-src` needs the `@alpinejs/csp` migration → **Deferred** |
| DB-P1 | **High** | App connects as a Postgres **superuser**; one role does DDL + DML | 📄 **Artifact** — `docs/db-roles.sql` (run on server) |
| DB-P2 | High | `5432/5433` published to host | 📄 **Artifact** — `compose.prod.yaml` (no host ports) |
| DEV-F1/F2/F3 | High | Dev compose is the only deploy file; Redis unauth; containers run root | 📄 **Artifact** — `compose.prod.yaml` + `docker/redis/*.conf` + `docker/php/php.prod.ini` |
| DEV-F4/F7 | High/Med | No TLS / security headers / rate limits in nginx | 📄 **Artifact** — `docker/nginx/prod.conf` |
| DB-P4 | Low | FK columns unindexed (Postgres doesn't auto-index FKs) | ✅ **Fixed** — migration `..._add_foreign_key_indexes` |
| DB-P5 | Low-Med | No `statement_timeout` | 📄 **Artifact** — `docs/db-roles.sql` |
| S-L3 | Low | `canAccessPanel` domain match case-sensitive | ✅ **Fixed** — case-insensitive (+ test) |
| S-L7 | Low | No boot-time `APP_KEY` assertion | ✅ **Fixed** — fails fast in production |
| S-L8 | Low | No app-level force-HTTPS | ✅ **Fixed** — `URL::forceScheme('https')` in production |
| S-L9 | Low | Admin uploads unbounded | ✅ **Fixed** — `maxSize` + `acceptedFileTypes` |
| FE5 | Med | Swiper A11y module not enabled | ✅ **Fixed** |
| FE3 | Med-High | Lead form: no error summary / focus on validation failure | ✅ **Fixed** — `role="alert"` summary + focus |
| FE9 | Low | FAQ accordion missing `aria-controls`/panel `id` | ✅ **Fixed** |
| QA-C1 | **High** | Panel-gate **denial** path untested | ✅ **Fixed** — `PanelAccessTest` |
| QA-C2/C3/C6 | Med | Honeypot / throttle / validation-edge untested | ✅ **Fixed** — `LeadFormTest` |
| QA-C4 | Med | Zero unit coverage of pure logic | ✅ **Fixed** — `SiteContentTest` |
| QA-S1/S2/S3 | High(infra) | No static analysis / CI / dep+secret scan | 📄 **Artifact** — `phpstan.neon` + `.github/workflows/ci.yml` |
| S-L6 | Low | No `composer audit` / `roave/security-advisories` | 📄 **Artifact** — `composer audit` is a CI gate; add the advisories pin when convenient |
| S-L4 | Low | No MFA on the panel | ⏳ **Deferred** — verify Filament v5 MFA API, then enable |
| S-L1/L2 | Low | Per-resource policies; Form Request for leads | ⏳ **Deferred** — N/A while single-admin; revisit if staff roles are added |
| FE6/FE7/FE8 | Med | Responsive images, code-split Swiper, self-host fonts | ⏳ **Deferred** — perf pass (needs an image pipeline) |
| DB-RLS, PgBouncer, AI-SDK, multi-tenant tests | — | — | ❌ **N/A** — single-tenant, no AI SDK, low concurrency |

---

## 3. Implemented this pass (code)

- `app/Http/Middleware/SecurityHeaders.php` — enforced `frame-ancestors`/`base-uri`/`object-src`; strict CSP Report-Only on public routes.
- `bootstrap/app.php` — `trustProxies` scoped to loopback + private ranges (env `TRUSTED_PROXIES`).
- `app/Models/User.php` — case-insensitive panel domain gate.
- `app/Providers/AppServiceProvider.php` — boot-time `APP_KEY` assertion + production force-HTTPS.
- `app/Filament/.../SiteSettingForm.php` — upload size/type bounds.
- `resources/views/components/ui/field.blade.php` — fixed the invisible-border regression.
- `resources/views/site/start.blade.php` — accessible error summary; `resources/js/app.js` focuses it.
- `resources/views/site/pricing.blade.php` — FAQ `aria-controls`/`id`.
- `resources/js/app.js` — Swiper `A11y` module.
- `database/migrations/..._add_foreign_key_indexes.php` — FK + `nav_links(location,sort)` indexes.
- `tests/` — `PanelAccessTest`, `LeadFormTest`, `SiteContentTest` (17 tests / 68 assertions green).

---

## 4. Deploy artifacts (apply on the server)

| File | Purpose |
|------|---------|
| `compose.prod.yaml` | Hardened stack — non-root, cap-drop, no host ports for DB/Redis, dev services excluded |
| `docker/nginx/prod.conf` | TLS, HSTS, security headers, rate limits, gzip |
| `docker/redis/redis-queue.conf` / `redis-cache.conf` | Auth, protected-mode, renamed dangerous commands, eviction policy |
| `docker/php/php.prod.ini` | `display_errors=Off`, `expose_php=Off`, OPcache, `disable_functions` |
| `docs/db-roles.sql` | Least-privilege Postgres roles (runtime vs migrator) + timeouts |
| `.github/workflows/ci.yml` | Pint → Larastan → tests → composer/npm audit → gitleaks |
| `phpstan.neon` | Static-analysis config (level 6) |

---

## 5. Runbook

### Zero-downtime deploy
```
0. Backup: pg_dump -Fc; tag current image :rollback. Confirm .env.prod present.
1. Build: docker compose -f compose.prod.yaml build app   (composer install --no-dev baked in)
2. Assets: npm ci && npm run build   (public/build shipped to nginx)
3. Migrate: docker compose -f compose.prod.yaml run --rm app php artisan migrate --force
4. Warm:  php artisan config:cache route:cache view:cache event:cache && php artisan filament:optimize
5. Roll:  docker compose -f compose.prod.yaml up -d --no-deps app queue nginx
          (opcache.validate_timestamps=0 → reload php-fpm: kill -USR2)
6. Verify: curl -fsS https://DOMAIN/up ; check /admin login ; docker compose ps healthy
```

### Rollback
```
1. php artisan down
2. docker tag creative-trees/app:rollback creative-trees/app:latest
3. docker compose -f compose.prod.yaml up -d --no-deps app queue nginx
4. If a migration must be reverted: php artisan migrate:rollback --force  (or restore pg_dump)
5. php artisan optimize:clear && php artisan filament:clear-cached-components ; re-warm
6. php artisan up ; verify /up + /admin
```

### Backup / restore
- Nightly `pg_dump -Fc` retained off-host (encrypted), plus WAL archiving for PITR.
- **Test a restore on a schedule** — an untested backup is not a backup.

### Secret rotation
- `APP_KEY`: rotating it invalidates encrypted data + sessions. Use Laravel's key-rotation (`APP_PREVIOUS_KEYS`) to decrypt old data while writing with the new key.
- DB/Redis passwords: update `.env.prod` + the role/`requirepass`, then `docker compose up -d` the affected services.

---

## 6. Definition of Done — status

**Security & access**
- [x] `APP_DEBUG=false`; no stack traces in non-local
- [x] `canAccessPanel()` enforced; unauthorized denied (test proves it)
- [x] Security headers present; CSP enforced (safe subset) + strict Report-Only — _strict enforce pending `@alpinejs/csp`_
- [x] CSRF/origin verification (`PreventRequestForgery`) enabled
- [x] Rate limiting on lead submit (+ nginx zones for `/admin/login`)
- [x] No secret exposed via `VITE_*` (only the public Reverb key)
- [x] Debug tooling absent in prod
- [ ] MFA on the panel — _deferred_
- [ ] Policy/Gate per model — _N/A (single-admin); revisit with staff roles_

**Data & realtime**
- [x] Least-priv DB roles documented (`docs/db-roles.sql`) — _apply on server_
- [x] No host ports for postgres/redis in `compose.prod.yaml`
- [x] Redis hardened config (auth, no-evict queue, renamed commands)
- [x] Broadcast channels default-deny / owner-scoped
- [ ] Backups + tested restore — _server task_

**Frontend**
- [x] `prefers-reduced-motion` honored; no keyboard/scroll traps
- [x] Form has error summary + focus; Swiper A11y; FAQ aria
- [x] All `{!! !!}` audited — every user-influenced sink is `e()`-escaped
- [ ] Lighthouse ≥ 95 / responsive images — _perf pass deferred_

**Platform & quality**
- [x] `compose.prod.yaml` excludes dev services; non-root; cap-drop; healthchecks
- [x] CI gates: pint, larastan, tests, composer/npm audit, gitleaks
- [ ] Container image scan (Trivy) — _optional CI job_
- [x] 17 tests / 68 assertions green (authz denial, honeypot, throttle, fallback)
