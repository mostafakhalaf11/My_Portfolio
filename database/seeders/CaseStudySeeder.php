<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\ProjectStage;
use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    /**
     * Client names stay out of these records on purpose — tenants are described
     * by size and sector only. `client_label` is the anonymised descriptor.
     */
    private const STUDIES = [
        [
            'slug' => 'amg-erp-multi-tenant-hrm-payroll',
            'title' => 'AMG ERP — Multi-Tenant HRM and Payroll Platform',
            'subtitle' => 'Turning a single-tenant monolith into a modular, company-scoped ERP',
            'client_label' => 'Enterprise HR software vendor',
            'role' => 'Lead engineer',
            'year_from' => 2025,
            'year_to' => null,
            'summary' => 'Modular Laravel ERP covering the full employee lifecycle across 9 feature modules: attendance rules, shift management, payroll with an Egyptian statutory layer, leave policies, and an asynchronous notification engine — now extending to employee mobile and desktop apps.',
            'context' => 'The platform began as a single-tenant monolith: roughly 150 flat controllers, 250 models, and a 2,800-line route file, with business rules duplicated across controllers. Tenant data was effectively global — most tables carried no company ownership at all — so a second customer could not be onboarded without risking cross-tenant data exposure.',
            'constraints' => 'The system was already in use, so nothing could be rebuilt from scratch and no migration could take the database offline. Payroll figures had to reconcile exactly against legacy payslips, since any drift is a payroll error someone notices on payday. The team was small, which ruled out approaches whose ongoing cost scaled with the number of tenants.',
            'options_rejected' => [
                [
                    'option' => 'Database per tenant',
                    'why_rejected' => 'Strong isolation, but every schema change becomes an N-database migration and every report a cross-database join. With a small team and frequent schema churn, the operational cost outgrew the isolation benefit.',
                ],
                [
                    'option' => 'Attaching users directly to a company',
                    'why_rejected' => 'The simplest model, but it cannot express one person working across several companies, which the business already required. It would have forced duplicate user accounts and split identity.',
                ],
                [
                    'option' => 'A global Eloquent scope applied to every model',
                    'why_rejected' => 'Convenient, but it silently hides rows in contexts that legitimately need cross-tenant reads, and failures are invisible rather than loud. Scoping was made explicit per query instead.',
                ],
            ],
            'decision' => 'Introduced a membership-based tenancy chain — User > Membership > Company > Workspace — so identity stays single while access is scoped per membership, and a workspace decides which module surface a member sees. Authorization was layered deliberately: RBAC for coarse permissions, ABAC for row-level query scoping, and a conditional-access engine for context rules on IP, country, and time window.',
            'implementation' => 'Roughly 40 formerly global tables were migrated to company-scoped ownership through forward-only, re-runnable data scripts, each paired with a reconciliation dry-run that reported what it would change before it changed anything. The monolith was cut into 9 self-contained feature modules with thin controllers over service layers, and shared domain engines were extracted so attendance, leave, and payroll rules had exactly one home. Business rules run as pluggable processors: 5 attendance rule domains emit penalties and compensations that feed the payroll pipeline as staged adjustments. In production the engines kept hardening: calendar-date arithmetic made DST-safe across attendance and payroll, hour-for-hour deduction for short hours, sick-leave pay bands, and batched payslip generation. Every sign-in or clock-in the conditional-access engine refuses now names the rule that refused it, so support can answer "why" without reading logs.',
            'outcomes' => [
                ['metric' => 'Legacy tables migrated', 'value' => '~40', 'note' => 'zero data loss, reconciliation dry-run per script'],
                ['metric' => 'Feature modules extracted', 'value' => '9', 'note' => 'from a single monolith'],
                ['metric' => 'Authorization layers', 'value' => '3', 'note' => 'RBAC, ABAC scoping, conditional access — every decision audited'],
                ['metric' => 'Commits authored', 'value' => '~1,320 of 1,900', 'note' => 'across 15 months'],
                ['metric' => 'First production cut-over', 'value' => '53-employee tenant', 'note' => 'rehearsal runbook and legacy payslip parity verification'],
                ['metric' => 'Interface languages', 'value' => '18', 'note' => 'admin-managed language packs, Arabic and Hebrew RTL included'],
            ],
            'architecture_diagram' => [
                'nodes' => [
                    ['id' => 'request', 'label' => 'HTTP request', 'group' => 'edge'],
                    ['id' => 'membership', 'label' => 'Membership resolution', 'group' => 'tenancy'],
                    ['id' => 'workspace', 'label' => 'Workspace context', 'group' => 'tenancy'],
                    ['id' => 'rbac', 'label' => 'RBAC permissions', 'group' => 'authz'],
                    ['id' => 'abac', 'label' => 'ABAC query scoping', 'group' => 'authz'],
                    ['id' => 'access', 'label' => 'Conditional access', 'group' => 'authz'],
                    ['id' => 'engine', 'label' => 'Domain engines', 'group' => 'domain'],
                    ['id' => 'payroll', 'label' => 'Payroll pipeline', 'group' => 'domain'],
                    ['id' => 'queue', 'label' => 'Queued listeners', 'group' => 'async'],
                ],
                'edges' => [
                    ['from' => 'request', 'to' => 'membership', 'label' => 'session or token'],
                    ['from' => 'membership', 'to' => 'workspace', 'label' => 'scopes'],
                    ['from' => 'workspace', 'to' => 'rbac', 'label' => ''],
                    ['from' => 'rbac', 'to' => 'abac', 'label' => 'narrows query'],
                    ['from' => 'abac', 'to' => 'access', 'label' => 'context check'],
                    ['from' => 'access', 'to' => 'engine', 'label' => 'allow'],
                    ['from' => 'engine', 'to' => 'payroll', 'label' => 'adjustments'],
                    ['from' => 'engine', 'to' => 'queue', 'label' => 'events'],
                ],
            ],
            'tech' => ['Laravel', 'PHP 8', 'MySQL 8', 'Redis', 'Sanctum', 'Queues', 'Filament', 'Blade', 'Alpine.js'],
            'live_url' => 'https://erp.amgeca.com',
            'stage' => ProjectStage::Live,
            'roadmap' => [
                'Employee mobile app for iOS and Android, plus a desktop app, built in Quasar (Vue) from one codebase.',
                'A versioned /api/v1 behind the apps: one JSON error shape, device binding, app attestation and push notifications.',
                'Idempotent clock-in and clock-out over the API, so a retry on a flaky mobile connection never records a punch twice.',
                'A desktop time tracker tied to projects, with ownership and photo checks.',
                'Conditional-access policies extended from web sessions to every app token request.',
            ],
            'is_featured' => true,
            'sort_order' => 0,
        ],
        [
            'slug' => 'stock-yard-commerce-rfq',
            'title' => 'Stock Yard — Bilingual B2B Commerce and RFQ Platform',
            'subtitle' => 'A contract-first Laravel 13 API behind a server-rendered Nuxt storefront',
            'client_label' => 'Freelance — sole architect and developer',
            'role' => 'Architect and developer',
            'year_from' => 2026,
            'year_to' => null,
            'summary' => 'Bilingual (EN/AR) B2B sourcing platform for industrial spare parts, tools and equipment: catalog, cash-on-delivery ordering with delivery or branch pickup, and request-for-quote. A Laravel 13 API with a Filament 5 operator panel and a Nuxt 4 SSR storefront.',
            'context' => 'A supplier needed to sell online in English and Arabic, take cash-on-delivery orders, and handle bulk enquiries that are negotiated rather than checked out. Off-the-shelf storefronts handled the catalog but not the quote workflow, and none of them modelled Arabic content as a first-class citizen.',
            'constraints' => 'One developer, so every abstraction had to earn its maintenance cost. Money and stock had to be exactly correct — no float arithmetic, no overselling. The shipping provider was manual at launch but would be replaced by an API integration later, so the boundary had to be drawn before it was needed.',
            'options_rejected' => [
                [
                    'option' => 'Microservices per domain',
                    'why_rejected' => 'Twelve domains would have meant twelve deployables, twelve pipelines, and distributed transactions across cart and inventory — all maintained by one person. The domain boundaries were kept, the process boundaries were not.',
                ],
                [
                    'option' => 'A generic repository layer over Eloquent',
                    'why_rejected' => 'It duplicates what Eloquent already does and makes every query indirect. Query objects were used only where reads were genuinely complex or reused.',
                ],
                [
                    'option' => 'Client-rendered SPA storefront',
                    'why_rejected' => 'A catalog lives or dies by search indexing, and bilingual pages double what needs crawling. Server-rendered Nuxt kept content in the initial HTML.',
                ],
            ],
            'decision' => 'Built a domain-driven modular monolith across 12 domains where Actions own transaction boundaries, side effects dispatch only after commit, and every external provider sits behind an application-owned contract — so shipping, search, and storage backends are swappable without touching domain code. The API contract came first: OpenAPI v1 is validated in CI on every push, and API Resources are the only serialization boundary.',
            'implementation' => 'Identity spans email OTP with hashing, expiry, throttling and replay prevention, Google OAuth with protected account linking, and TOTP MFA with encrypted recovery codes. Catalog and inventory sit on transactional stock aggregates over an append-only movement ledger, with reservation-backed cart limits. Money is stored as integer minor units and public identifiers are ULIDs, so there is no float drift and no ID enumeration. Checkout offers delivery or branch pickup; guarded status transitions release reserved stock exactly once on rejection and consume it exactly once on delivery, and operators get actionable notifications that link straight to the work item. Locally the stack runs in Docker. Staging runs natively on an OVH VPS behind a Cloudflare Tunnel, so the origin IP never appears in DNS, with Cloudflare Access in front of the storefront and admin, automatic deploys from main, and nightly encrypted database backups to Cloudflare R2.',
            'outcomes' => [
                ['metric' => 'OpenAPI paths', 'value' => '47', 'note' => 'contract-first, validated in CI on every push'],
                ['metric' => 'Backend tests', 'value' => '190+', 'note' => 'PHPUnit, with Larastan level 6 and Pint in CI'],
                ['metric' => 'CI/CD workflows', 'value' => '9', 'note' => 'quality, contract, security, release readiness, staging deploy'],
                ['metric' => 'Domains', 'value' => '12', 'note' => 'Actions own transaction boundaries'],
                ['metric' => 'Public web ports on the origin', 'value' => '0', 'note' => 'Cloudflare Tunnel, with Access in front of staging'],
                ['metric' => 'Database backups', 'value' => 'Nightly, encrypted', 'note' => 'shipped off-server to Cloudflare R2'],
            ],
            'architecture_diagram' => [
                'nodes' => [
                    ['id' => 'storefront', 'label' => 'Nuxt 4 SSR storefront', 'group' => 'edge'],
                    ['id' => 'admin', 'label' => 'Filament 5 panel', 'group' => 'edge'],
                    ['id' => 'api', 'label' => 'Laravel /api/v1', 'group' => 'api'],
                    ['id' => 'action', 'label' => 'Domain Actions', 'group' => 'domain'],
                    ['id' => 'inventory', 'label' => 'Inventory ledger', 'group' => 'domain'],
                    ['id' => 'orders', 'label' => 'Orders and quotes', 'group' => 'domain'],
                    ['id' => 'provider', 'label' => 'Shipping provider contract', 'group' => 'boundary'],
                    ['id' => 'queue', 'label' => 'Queued side effects', 'group' => 'async'],
                ],
                'edges' => [
                    ['from' => 'storefront', 'to' => 'api', 'label' => 'same-origin proxy'],
                    ['from' => 'admin', 'to' => 'action', 'label' => 'same Actions'],
                    ['from' => 'api', 'to' => 'action', 'label' => ''],
                    ['from' => 'action', 'to' => 'inventory', 'label' => 'reserve'],
                    ['from' => 'action', 'to' => 'orders', 'label' => 'commit'],
                    ['from' => 'orders', 'to' => 'provider', 'label' => 'after commit'],
                    ['from' => 'action', 'to' => 'queue', 'label' => 'domain events'],
                ],
            ],
            'tech' => ['Laravel 13', 'PHP 8.5', 'Filament 5', 'Nuxt 4', 'Vue 3', 'TypeScript', 'MySQL 8.4', 'Redis 7', 'Cloudflare', 'Docker', 'OpenAPI'],
            // The repository is private and staging sits behind Cloudflare Access: link only what a visitor can open.
            'repo_url' => null,
            'live_url' => 'https://stock-yard.com',
            'stage' => ProjectStage::Staging,
            'roadmap' => [
                'Public launch on stock-yard.com. The coming-soon page is live; the full platform is in private staging with the owner.',
                'Production on the same Cloudflare Tunnel layout as staging, with its own storefront and admin hostnames.',
                'Cloudflare Turnstile switched on for registration, OTP and the contact form. It already ships behind config.',
                'A second security-hardening pass (P1 and P2) before launch.',
                'A shipping-provider API integration behind the existing provider contract, replacing the manual flow.',
            ],
            'is_featured' => true,
            'sort_order' => 1,
        ],
    ];

    public function run(): void
    {
        foreach (self::STUDIES as $study) {
            CaseStudy::updateOrCreate(
                ['slug' => $study['slug']],
                [...$study, 'status' => ContentStatus::Published, 'published_at' => now()],
            );
        }
    }
}
