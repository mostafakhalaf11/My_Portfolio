<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /** Public so content migrations can apply the same copy to databases seeded earlier. */
    public const ENTRIES = [
        [
            'title' => 'Full-Stack Developer',
            // Spelled as entered in /admin on 2026-11-03.
            'company' => '78s Works',
            'company_url' => 'https://www.78sworks.com/en',
            'location' => 'Giza, Egypt',
            'employment_type' => 'Full-time',
            'started_on' => '2025-07-01',
            'ended_on' => null,
            'summary' => 'Enterprise HR and ERP software.',
            // No exact internal figures (table, module, tenant or commit counts): employer data stays private.
            'highlights' => [
                'Lead engineer on the company\'s multi-tenant HRM/Payroll ERP (~70% of commits over 15 months): designed its tenancy model and moved the legacy data to company-scoped ownership with zero data loss.',
                'Engineered three authorization layers: RBAC permissions, ABAC query scoping with integrity checks, and a conditional-access engine on IP, country and time windows, with every decision audited.',
                'Building the versioned API behind the ERP\'s mobile and desktop apps: one JSON error shape, device binding, app attestation, push notifications and idempotent clock-in.',
                'Led the first client production cut-over, with a rehearsal runbook and payslip parity verified against the legacy system.',
            ],
            'tech' => ['Laravel', 'PHP 8', 'MySQL 8', 'Redis', 'Sanctum', 'Filament', 'Blade', 'Alpine.js'],
        ],
        [
            'title' => 'Backend Developer (Freelance)',
            'company' => 'KapyApp',
            'company_url' => 'http://www.kapyapp.com/',
            'location' => 'Cairo, Egypt',
            'employment_type' => 'Freelance',
            'started_on' => '2025-05-01',
            'ended_on' => '2025-07-31',
            'summary' => 'Online food delivery platform.',
            'highlights' => [
                'Shipped a complete restaurant delivery platform in Laravel in under 3 months, covering ordering, delivery tracking and authenticated REST APIs.',
                'Automated deployment on Hostinger with GitHub auto-deploy and CI/CD, cutting release effort to a single push.',
                'Diagnosed and fixed production issues across image storage, mail delivery and configuration caching.',
            ],
            'tech' => ['Laravel', 'REST APIs', 'MySQL', 'GitHub Actions'],
        ],
        [
            'title' => 'Backend Developer',
            'company' => 'TryOrder',
            'company_url' => 'http://www.tryorder.com/',
            'location' => 'Riyadh, Saudi Arabia',
            'employment_type' => 'Full-time',
            'started_on' => '2024-09-01',
            'ended_on' => '2025-04-30',
            'summary' => 'Online food delivery platform.',
            'highlights' => [
                'Maintained and debugged CRUD operations across Laravel REST APIs and cleared escalated technical support tickets.',
            ],
            'tech' => ['Laravel', 'REST APIs', 'MySQL'],
        ],
        [
            'title' => 'ERP End-User Specialist',
            'company' => 'KB-Denim',
            'company_url' => 'https://kbdenim.com/',
            'location' => 'Cairo, Egypt',
            'employment_type' => 'Full-time',
            'started_on' => '2023-06-01',
            'ended_on' => '2024-09-30',
            'summary' => 'Denim manufacturing.',
            'highlights' => [
                'Streamlined invoice processing in Microsoft Dynamics ERP, cutting handling time 30% (10 to 7 minutes per invoice).',
                'Raised data-entry accuracy 15% through workflow improvements and removed 5 hours of manual reporting per week.',
            ],
            'tech' => ['Microsoft Dynamics ERP'],
        ],
    ];

    public function run(): void
    {
        foreach (self::ENTRIES as $index => $entry) {
            Experience::updateOrCreate(
                ['company' => $entry['company'], 'title' => $entry['title']],
                [...$entry, 'sort_order' => $index, 'status' => ContentStatus::Published],
            );
        }
    }
}
