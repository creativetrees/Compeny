<div align="center">

# Creative Trees Group

**Digital product studio & IT ecosystem — company profile.**
Monochrome, monospace, motion-driven. Built on Laravel + Filament.

</div>

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.5) |
| Admin | Filament v5 → `/admin` |
| Database | PostgreSQL 17 |
| Cache / Queue / Sessions | Redis 7 (config-driven, falls back to database) |
| Real-time | Laravel Reverb (websockets) |
| Frontend | Blade + Vite + Tailwind v4 + Alpine.js |
| Motion | GSAP + ScrollTrigger + Lenis (smooth scroll) |
| Dev infra | Docker Compose (postgres, redis, reverb, queue, vite, mailpit) |

The application is **config-driven**: with only a database available it runs fully;
Redis and Reverb are optional enhancements. See [`docs/DEPLOYMENT-cpanel.md`](docs/DEPLOYMENT-cpanel.md).

## Quick start

### Option A — hybrid (recommended): host PHP + Docker services

```bash
# 1. Bring up data services (Postgres on :5433, Redis on :6379)
make up                 # or: docker compose up -d postgres redis

# 2. Install & build
composer install
npm install && npm run dev

# 3. Migrate + seed demo content
php artisan migrate --seed

# 4. Serve
php artisan serve       # http://localhost:8000
```

> Postgres is published on host port **5433** to avoid clashing with a local
> PostgreSQL on 5432. The app's `.env` already points at `127.0.0.1:5433`.

### Option B — full Docker

```bash
make build && make up   # everything in containers
make fresh              # migrate:fresh --seed inside the app container
```

## Admin

| URL | Credentials (dev seed) |
|---|---|
| `/admin` | `admin@creativetrees.group` / `password` |

## Project layout

```
app/
  Filament/          # admin panel resources, widgets, pages
  Http/Controllers/  # public site controllers
  Models/            # Eloquent models
  Events/            # broadcast events (Reverb)
resources/
  views/site/        # public Blade pages + sections
  views/components/  # reusable UI components
  css/  js/          # Tailwind, GSAP, Lenis, Alpine
database/            # migrations, factories, seeders
docker/              # php (Dockerfile), nginx
docs/                # BLUEPRINT.md, DEPLOYMENT-cpanel.md, DESIGN-SYSTEM.md
```

## Common commands

Run `make help` for the full list. Highlights:

| Command | Action |
|---|---|
| `make up` / `make down` | start / stop the dev stack |
| `make fresh` | wipe DB, re-migrate, re-seed |
| `make test` | run the Pest suite |
| `make pint` | format code (Laravel Pint) |
| `make assets` | build production frontend assets |

## License

Proprietary — © Creative Trees Group.
