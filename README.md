# Portfolio — Mostafa Khalaf Mohamed

Backend engineer's portfolio, built as a working system rather than a static page:
a Laravel 13 + Inertia + Vue 3 site whose content is administered through Filament 5.

**Stack:** Laravel 13 · PHP 8.4 · Inertia 2 · Vue 3.5 · Tailwind 4 · Vite 8 · Filament 5 · SQLite (production, tests) · MySQL 8 (optional locally)
**Quality gates:** Pest 4 · Larastan (level 6) · Laravel Pint

## Why it is built this way

The site is one Laravel application with two front doors — an Inertia/Vue public site and a
Filament panel at `/admin` — rather than a separate API and SPA. For a single-author site the
split costs duplicated auth, CORS, and a second router while buying nothing.

Content is **typed tables, not a generic page builder**. Case studies, experiences, skills and
credentials each have their own schema; only homepage composition is flexible, via
`page_sections`. Generic block builders look powerful for a week and rot afterwards.

Case studies carry a **structured narrative** — context, constraints, options rejected,
decision, implementation, measured outcomes — because the reasoning is the part worth reading.
The "options rejected" section is a first-class column, not an afterthought.

`skills` has a `years` column and no `percentage` column. Skill bars are decoration; the
schema refuses to invite them.

## Architecture

```
app/
├── Enums/ContentStatus.php        draft | published, shared by every content model
├── Filament/Resources/            admin CRUD (CaseStudyForm is hand-written, not generated)
├── Http/Controllers/              thin: shape Inertia props, nothing else
└── Models/                        scopes: published(), ordered(), visible()

resources/js/
├── components/ArchitectureDiagram.vue   inline SVG laid out from CMS node/edge data
├── components/SiteShell.vue             header, nav, theme toggle, footer
└── pages/                               Home, CaseStudies/Show
```

Diagrams are **computed from data, not hand-positioned**: a case study stores nodes and edges,
and the SVG lays them out in columns with bezier connectors. Editing the diagram is a CMS task.

The design tokens live once, in `resources/css/app.css` — dark-first surfaces, a signal accent,
wire lines, and a `.bg-blueprint` grid drawn with two gradients and zero image requests. Motion
is disabled globally under `prefers-reduced-motion`.

## Local setup

Requires PHP 8.3+, Composer 2, Node 20+, and MySQL 8.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Set `DB_*` and the admin variables in `.env`:

```
PORTFOLIO_ADMIN_EMAILS=you@example.com
PORTFOLIO_ADMIN_NAME="Your Name"
PORTFOLIO_ADMIN_PASSWORD=choose-a-strong-one
```

Then build the database and assets:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

`AdminUserSeeder` reads the password from the environment and **skips entirely when it is
unset**, so a server that was never configured does not get an account with a default password.

## Admin access

Two gates protect `/admin`. A user row is not sufficient: `User::canAccessPanel()` also requires
the address to appear in `config/portfolio.php` → `admin_emails`. A stray or seeded account
cannot administer the site.

## Tests

```bash
vendor/bin/pest              # feature suite
vendor/bin/phpstan analyse   # Larastan, level 6
vendor/bin/pint --test       # formatting
```

Tests run against in-memory SQLite, the engine production runs on — testing on a different
engine than production hides differences that matter. PHP needs the `pdo_sqlite` extension.

Page tests assert **Inertia props**, not rendered HTML. Without SSR the markup lives in the Vue
bundle, so asserting on server HTML tests nothing.

One test exists purely as a guard: `it('keeps credentials out of version-controlled source')`
scans the tracked tree for the admin password on every run.

## Link previews and SEO

Link-preview bots (LinkedIn, WhatsApp, X, Slack) and search crawlers never run JavaScript, so
every page's title, description, Open Graph and Twitter tags, canonical URL and schema.org
JSON-LD are printed by the server. Controllers build them with `App\Support\PageMeta` and pass
them as the `meta` prop; `app.blade.php` prints them, and Vue's `<Head>` reuses the same values
on client-side navigation. `/sitemap.xml` and `/robots.txt` are generated from the current host.

The preview images in `public/og/` are rendered from `resources/og/card.html` with the live
content. After changing a project's title, subtitle, stage or tech:

```bash
php artisan og:render   # needs a local Chrome or Edge; then commit public/og/
```

Platforms cache previews for days. To refresh one after a change, paste the URL into the
[LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/) or the
[Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/) (WhatsApp uses the
same cache).

## Deployment — Azure App Service, $0/month

Production is an App Service **Free (F1)** Linux plan on PHP 8.4, defined in `infra/` (Bicep)
and deployed by `.github/workflows/deploy.yml` on every push to `main` once the gates pass.

Why it is shaped this way:

- **F1 is the only App Service tier that is free for the life of the subscription.** It costs a
  `*.azurewebsites.net` address, cold starts after ~20 idle minutes, 60 CPU-minutes a day and
  1 GB of disk — all acceptable for a portfolio.
- **SQLite instead of a managed database.** Every managed database on Azure is billed after its
  free year; this site is single-author and read-mostly. The file lives at
  `/home/data/portfolio.sqlite`, outside `wwwroot`, so deployments never touch it. Sessions use
  cookies so page views never write to it.
- **A $1 subscription budget is the tripwire.** The expected bill is $0.00, so any alert means
  something billable exists.
- `deploy/azure/startup.sh` runs on every container start: it points nginx at `public/`,
  migrates, seeds **only a brand-new database** (afterwards the admin panel owns the content),
  and rebuilds caches from the App Service settings.

First-time setup (PowerShell, after `az login`):

```powershell
$env:PORTFOLIO_APP_KEY = (php artisan key:generate --show)
$env:PORTFOLIO_ADMIN_PASSWORD = '<a strong password>'

# Preview, then create. Nothing is created by what-if.
az deployment sub what-if --location francecentral --template-file infra/main.bicep --parameters infra/main.bicepparam
az deployment sub create  --location francecentral --template-file infra/main.bicep --parameters infra/main.bicepparam

# Publish profile for GitHub Actions (store as the AZURE_WEBAPP_PUBLISH_PROFILE secret)
az webapp deployment list-publishing-profiles -g rg-portfolio -n mostafa-khalaf --xml
```

Then in GitHub → Settings → Secrets and variables → Actions, add the variable
`AZURE_WEBAPP_NAME` and the secret `AZURE_WEBAPP_PUBLISH_PROFILE`, and push to `main`.

**Moving to a custom domain later** needs no code change: the host only exists in the `APP_URL`
app setting. Either move the plan to B1 (about $13/month) and bind the domain with a free App
Service managed certificate, or keep F1 behind a Cloudflare Worker proxy.

## Roadmap

- [x] Foundation: Laravel 13, Inertia, Vue, Tailwind, Filament, CI-ready quality gates
- [x] Content schema, Filament resources, seeded from a single résumé source
- [x] Public site: hero diagram, case studies, stack, experience, responsive, dark/light
- [ ] Remaining Filament forms (only `CaseStudyForm` is hand-written so far)
- [ ] OpenAPI docs + live API playground
- [ ] ABAC policy simulator
- [ ] Contact form with honeypot + Turnstile
- [x] Deployment pipeline: GitHub Actions → Azure App Service (F1, $0/month)
- [x] Link previews (Open Graph, Twitter cards, rendered card images), JSON-LD, sitemap, robots
- [ ] SSR
