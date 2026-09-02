<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    private const ENTRIES = [
        [
            'title' => 'Full-Stack Developer',
            'company' => '78sWorks',
            'company_url' => 'https://www.78sworks.com/en',
            'location' => 'Giza, Egypt',
            'employment_type' => 'Full-time',
            'started_on' => '2025-07-01',
            'ended_on' => null,
            'summary' => 'Enterprise HR and ERP software.',
            'highlights' => [
                'Led delivery of the company multi-tenant HRM/Payroll ERP, authoring ~1,190 of 1,450 commits in 14 months.',
                'Designed the tenancy backbone (User > Membership > Company > Workspace) and migrated ~40 legacy global tables to company-scoped ownership using forward-only scripts and reconciliation dry-runs, with zero data loss.',
                'Re-architected a monolithic codebase into 9 feature modules with thin controllers, service layers and shared domain engines, removing duplicated business logic.',
                'Engineered 3 authorization layers: RBAC permissions, ABAC query scoping with integrity checks, and a conditional-access engine on IP, country and time windows, with every decision audited.',
                'Hardened authentication with device and IP risk scoring over HMAC-hashed fingerprints, 30-minute signed confirm/report links, account lockdown and session revocation.',
                'Led the first client production cut-over for a 53-employee tenant, covering rehearsal runbook, pre-cut-over checklist and legacy payslip parity verification.',
                'Authored the engineering conventions and reusable delivery skills the team codes against, driving specification-first delivery across a 199-task board.',
            ],
            'tech' => ['Laravel', 'PHP 8', 'MySQL 8', 'Redis', 'Filament', 'Blade', 'Alpine.js'],
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
