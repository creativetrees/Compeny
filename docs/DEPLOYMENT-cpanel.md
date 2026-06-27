# Deployment Guide — cPanel (PostgreSQL, full access)

This guide deploys **Creative Trees Group** (Laravel 12 + Filament) to a cPanel host
that has PostgreSQL and shell/cron access. The app is **config-driven**, so Redis and
Reverb are optional — the site runs fully without them (only real-time notifications
degrade gracefully).

---

## 0. Prerequisites on the host

- PHP 8.2+ (8.5 recommended) with extensions: `pdo_pgsql`, `mbstring`, `openssl`,
  `bcmath`, `ctype`, `fileinfo`, `tokenizer`, `xml`, `curl`, `gd`, `zip`, `intl`.
- PostgreSQL database + user (create via **cPanel → PostgreSQL Databases**).
- Composer (cPanel Terminal) and the ability to set the document root.
- (Optional) Redis + Supervisor/long-running processes for queue & Reverb.

---

## 1. Build assets locally, then upload

Frontend assets are compiled locally (cPanel rarely has Node):

```bash
npm install
npm run build        # outputs to public/build
```

Upload the project (or `git pull` on the server). Do **not** upload `node_modules`.
`vendor/` can be uploaded or installed on the server (next step).

---

## 2. Install PHP dependencies on the server

```bash
cd ~/creative-trees
composer install --no-dev --optimize-autoloader
```

---

## 3. Point the domain document root to `public/`

In **cPanel → Domains**, set the document root to `…/creative-trees/public`.

If you cannot change the document root (addon domain quirks), use this `.htaccess`
in the web root to forward into `public/` — but changing the document root is preferred.

```apache
# web root .htaccess (fallback only)
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

---

## 4. Environment configuration

Copy `.env.example` → `.env` and set:

```dotenv
APP_NAME="Creative Trees Group"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cpaneluser_creativetrees
DB_USERNAME=cpaneluser_ctg
DB_PASSWORD=********

# Safe fallbacks when Redis is NOT available on the host:
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# If Redis IS available, switch the three above to `redis` and set:
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# Broadcasting: 'log' disables real-time cleanly; 'reverb' enables it.
BROADCAST_CONNECTION=log

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@yourdomain.com
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
```

Then:

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force        # optional: demo content
php artisan storage:link
```

---

## 5. Cache for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Re-run `php artisan optimize:clear` then re-cache after each deploy.

---

## 6. Queue worker (database driver) via cron

If you keep `QUEUE_CONNECTION=database`, process jobs with a cron entry
(**cPanel → Cron Jobs**, every minute):

```cron
* * * * * cd /home/cpaneluser/creative-trees && php artisan schedule:run >> /dev/null 2>&1
```

For dedicated workers (if Supervisor is available):

```ini
[program:creative-trees-queue]
command=php /home/cpaneluser/creative-trees/artisan queue:work --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=1
user=cpaneluser
```

---

## 7. (Optional) Real-time with Reverb

Only if the host allows long-running processes (Supervisor / PM2):

```ini
[program:creative-trees-reverb]
command=php /home/cpaneluser/creative-trees/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=cpaneluser
```

Set in `.env`:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

Proxy `wss://yourdomain.com/app` → `127.0.0.1:8080` (Apache `mod_proxy_wstunnel`).
If real-time is not available, leave `BROADCAST_CONNECTION=log` — the admin still
receives new leads, just without live toasts.

---

## 8. Post-deploy checklist

- [ ] `https://yourdomain.com` loads the site (assets from `public/build`).
- [ ] `https://yourdomain.com/admin` reaches the Filament login.
- [ ] A test lead from **Start a Project** is stored and emailed.
- [ ] `storage/` and `bootstrap/cache/` are writable (755/775).
- [ ] `APP_DEBUG=false` in production.
- [ ] HTTPS enforced.
