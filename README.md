# Portfolio — Mostafa Khalaf Mohamed

Backend engineer's portfolio, built as a working system rather than a static page:
a Laravel 13 + Inertia + Vue 3 site whose content is administered through Filament 5.

**Stack:** Laravel 13 · PHP 8.4 · Inertia 2 · Vue 3.5 · Tailwind 4 · Vite 8 · Filament 5 · MySQL 8
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

Tests run against MySQL (`portfolio_testing`), not SQLite — testing on a different engine than
production hides differences that matter.

Page tests assert **Inertia props**, not rendered HTML. Without SSR the markup lives in the Vue
bundle, so asserting on server HTML tests nothing.

One test exists purely as a guard: `it('keeps credentials out of version-controlled source')`
scans the tracked tree for the admin password on every run.

## Roadmap

- [x] Foundation: Laravel 13, Inertia, Vue, Tailwind, Filament, CI-ready quality gates
- [x] Content schema, Filament resources, seeded from a single résumé source
- [x] Public site: hero diagram, case studies, stack, experience, responsive, dark/light
- [ ] Remaining Filament forms (only `CaseStudyForm` is hand-written so far)
- [ ] OpenAPI docs + live API playground
- [ ] ABAC policy simulator
- [ ] Contact form with honeypot + Turnstile
- [ ] SSR, dynamic OG images, sitemap, deployment pipeline
