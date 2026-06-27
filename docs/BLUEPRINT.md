# Creative Trees Group — Blueprint

> Company profile & digital product studio. Monochrome, monospace, brutalist-minimal
> (referensi: Kiyro). Backend Laravel + Filament + PostgreSQL + Redis + Reverb.
> Frontend server-rendered Blade dengan animasi kaya & smooth. Deploy: cPanel (full access, PostgreSQL).

Status: **DRAFT — menunggu persetujuan.** Tanggal: 2026-06-27.

---

## 1. Positioning & Pesan

**Creative Trees Group** — studio produk digital & ekosistem IT profesional.
Tone monospace/brutalist-clean: tegas, percaya diri, banyak ruang napas.

- **Hero headline (gaya Kiyro):** "WE GROW DIGITAL PRODUCTS THAT SCALE"
- **Eyebrow:** `● DIGITAL PRODUCT STUDIO & IT ECOSYSTEM ›`
- **Sub:** "We help startups and teams turn ideas into powerful digital products —
  from strategy and design to scalable engineering."
- **Primary CTA:** `START A PROJECT ●`

---

## 2. Arsitektur

```
┌──────────────────────── DEV (Docker Compose) ────────────────────────┐
│  app (PHP 8.5 / Laravel 12)   nginx   vite (Node 26)                  │
│  postgres 17   redis 7   reverb (ws)   queue-worker   mailpit         │
└──────────────────────────────────────────────────────────────────────┘
                                  │  deploy
┌──────────────────────── PROD (cPanel, full access) ──────────────────┐
│  PHP 8.x   PostgreSQL   (Redis opsional)   Reverb via Supervisor/PM2  │
│  Queue worker via cron/supervisor   Assets pre-built (npm run build)  │
└──────────────────────────────────────────────────────────────────────┘
```

**Prinsip config-driven (jaring pengaman cPanel):**
- `CACHE_STORE` → redis (dev) / file|database (fallback)
- `QUEUE_CONNECTION` → redis (dev) / database (fallback)
- `BROADCAST_CONNECTION` → reverb (jika jalan) / log (fallback, fitur degrade mulus)
- `SESSION_DRIVER` → redis / database

Artinya: kalau di cPanel Redis/Reverb tidak aktif, situs tetap jalan penuh — hanya
notifikasi real-time yang non-aktif. Tidak ada hard dependency yang bikin error.

---

## 3. Tech Stack

| Layer | Pilihan | Catatan |
|---|---|---|
| Bahasa | PHP 8.5 | sudah terpasang |
| Framework | Laravel 12 | via Laravel Installer 5.28 |
| Admin | Filament v4 | resources, dashboard, lead management |
| DB | PostgreSQL 17 | dev via Docker, prod via cPanel |
| Cache/Queue/Session | Redis 7 | config-driven fallback ke database/file |
| Real-time | Laravel Reverb | websocket, notif lead live |
| Build | Vite + Tailwind CSS v4 | |
| Interaktivitas | Alpine.js | ringan, cocok dengan Blade |
| Animasi | GSAP + ScrollTrigger | scroll-driven, timeline |
| Smooth scroll | Lenis | "buttery scroll" |
| Slider/marquee | Swiper / custom CSS | logo marquee, testimonial |
| Email | Laravel Mail (Mailpit dev) | notifikasi lead |
| Testing | Pest | feature + unit |

**Tipografi (monokrom Kiyro):**
- Display/Heading: monospace bold — **JetBrains Mono** / **Geist Mono** (huruf besar, tracking lebar)
- Body: monospace kecil atau grotesk netral (**Geist** / **Inter**) — final dipilih saat build
- Palet: `#0A0A0A` (ink), `#FFFFFF` (paper), `#F4F4F2` (panel), abu netral. Tanpa warna lain (sesuai "mirip Kiyro").

---

## 4. Sitemap & Halaman

| Halaman | Route | Isi |
|---|---|---|
| Home | `/` | hero, trusted-by logos, services/features, work preview, process, pricing, testimonial, CTA |
| Work | `/work` | grid portofolio + filter kategori |
| Case Study | `/work/{slug}` | detail proyek, galeri, hasil, services used |
| Services | `/services` | layanan + detail |
| Process | `/process` | tahapan kerja (Discover → Design → Build → Scale) |
| Pricing | `/pricing` | paket harga |
| Products | `/products` | katalog produk/aset (lead, bukan checkout) |
| Team | `/team` | profil tim |
| About | `/about` | cerita, nilai, ekosistem |
| Start a Project | `/start` | form lead bertahap |
| Contact | `/contact` | info kontak + form |
| Legal | `/privacy`, `/terms` | halaman legal |
| Admin | `/admin` | Filament |

Insights/Blog ditandai opsional (v1.5) — bisa diaktifkan jika diinginkan.

---

## 5. Animasi (PHP/Blade tetap bisa "wah")

Semua server-rendered (cPanel-friendly), tanpa SPA:
- **Lenis** smooth scroll global.
- **GSAP + ScrollTrigger**: reveal per-section, parallax halus, pin/timeline pada hero & process.
- **Text scramble/decode** pada heading (efek karakter ala grid di referensi Kiyro).
- **Magnetic buttons** & **custom cursor** (desktop).
- **Marquee** logo "Trusted by".
- **Counter** angka statistik.
- **Page transitions** halus (View Transitions API / fade overlay) walau full reload.
- **Stagger** pada grid work & list.
- `prefers-reduced-motion` dihormati penuh (semua animasi punya fallback statis).

---

## 6. Skema Data (model utama)

- **projects** — title, slug, client, category_id, year, cover, gallery(json), excerpt, body, services(json), url, is_featured, sort, status
- **categories** — name, slug (untuk projects/products)
- **services** — title, slug, icon, summary, body, sort
- **products** — title, slug, type, summary, body, price_label, features(json), cta_url, sort, status
- **team_members** — name, role, bio, photo, socials(json), sort
- **clients** — name, logo, url, sort (trusted-by)
- **testimonials** — author, role, company, quote, avatar, project_id?, sort
- **leads** — name, email, company, phone, budget, service_interest, message, status(new/contacted/won/lost), source, meta(json)
- **settings** — singleton (hero copy, kontak, sosmed, SEO default) via Filament
- **posts** — (opsional blog) title, slug, cover, excerpt, body, published_at
- **users** — admin Filament

Factory + seeder untuk semua, supaya situs langsung "berisi" saat demo.

---

## 7. Filament Admin

Resources: Projects, Categories, Services, Products, Team, Clients, Testimonials,
Leads (status board + filter), Posts(opsional), Settings (singleton), Users.

- **Dashboard**: widget statistik (leads baru, proyek, konversi), grafik, daftar lead terbaru.
- **Real-time (Reverb)**: lead baru → broadcast → toast notification + badge + suara di panel admin.
- Role sederhana (admin) — bisa diperluas nanti.

---

## 8. Struktur Folder (ringkas)

```
app/
  Filament/Resources/...        # admin
  Models/...                    # Project, Service, Lead, ...
  Http/Controllers/Site/...     # Home, Work, Services, Lead, ...
  Events/LeadReceived.php       # broadcast Reverb
resources/
  views/site/...                # blade pages + sections + components
  views/components/...          # ui components (button, marquee, reveal)
  css/  js/                     # tailwind, gsap, lenis, alpine
database/migrations|factories|seeders/
docker/                         # nginx, php, compose
routes/web.php
docs/                           # blueprint, deployment, design-system
```

---

## 9. Milestone / Tahapan Build

1. **Foundation** — Laravel 12 + Docker (app, nginx, postgres, redis, reverb, vite, mailpit), Filament install, git init, .env.
2. **Data layer** — migrations, models, relations, factories, seeders (situs langsung berisi).
3. **Admin** — Filament resources + dashboard + settings + lead board + notif Reverb.
4. **Frontend foundation** — layout, design system (tokens, fonts), Tailwind/Vite, Alpine + GSAP + Lenis.
5. **Pages** — Home (semua section) → Work + Case Study → Services → Process → Pricing → Products → Team → About → Start/Contact.
6. **Animasi polish** — reveal, scramble, magnetic, marquee, cursor, transitions, reduced-motion.
7. **Lead flow** — form → validasi → simpan → email + Reverb notify → thank-you.
8. **SEO/Perf** — meta/OG, sitemap, robots, cache, optimasi gambar, Lighthouse.
9. **Deploy** — panduan cPanel (PostgreSQL, env, build asset, queue/reverb via supervisor/cron) + catatan Docker prod.
10. **QA** — code-review plugin, Pest tests, dokumentasi.

---

## 10. Open choices (default saya, bisa diubah)

- Blog/Insights: **off** untuk v1 (struktur disiapkan, aktif di v1.5).
- Font final: **JetBrains Mono** (display) — konfirmasi saat build.
- Bahasa konten situs: **English** (gaya Kiyro) — atau Indonesia/bilingual jika mau.
- Multi-agent: saya pakai agen spesialis (design, content, backend, frontend) per fase
  dengan checkpoint review, sesuai permintaan "ajak team".
