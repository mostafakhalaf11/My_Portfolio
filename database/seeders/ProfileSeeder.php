<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::updateOrCreate(['email' => 'mostafkhalaf11@gmail.com'], [
            'name' => 'Mostafa Khalaf Mohamed',
            'headline' => 'Full-Stack Software Engineer',
            'title' => 'Laravel | Vue | Multi-Tenant SaaS',
            'summary' => 'Full-Stack Software Engineer with 2+ years of experience building multi-tenant SaaS platforms in Laravel and Vue. Lead engineer on a modular HRM/Payroll ERP, owning its tenancy architecture, attendance and payroll engines and authorization layers, and now the versioned API behind its employee mobile and desktop apps. Sole architect of a bilingual B2B commerce platform on Laravel 13 and Nuxt 4, running in Cloudflare-protected staging ahead of launch. Strong in domain modeling, policy-driven business engines, data migration under production constraints, and API design that stays maintainable as systems grow. Comfortable owning delivery from schema design through CI/CD, deployment and backups.',

            'phone' => '+20 115 347 0446',
            'phone_link' => 'tel:+201153470446',
            'location' => 'Cairo, Egypt',
            'github_url' => 'https://github.com/mostafakhalaf11',
            'linkedin_url' => 'https://www.linkedin.com/in/mostafa-khalaf-3b003621a/',
            // Relative to public/. The download button stays hidden until the PDF is there.
            'resume_path' => 'cv/Mostafa_Khalaf_Mohamed_Resume.pdf',

            'languages' => [
                ['name' => 'Arabic', 'level' => 'Native'],
                ['name' => 'English', 'level' => 'Professional'],
                ['name' => 'German', 'level' => 'Basic'],
            ],

            'strengths' => [
                'Problem diagnosis and troubleshooting across Laravel and server-side systems',
                'Technical documentation and specification writing',
                'Sprint-based Agile delivery',
                'Cross-functional collaboration',
                'Independent learning',
                'Ownership of outcomes',
            ],

            'seo' => [
                'title' => 'Mostafa Khalaf Mohamed — Full-Stack Software Engineer',
                'description' => 'Full-stack engineer building multi-tenant SaaS platforms in Laravel and Vue. Multi-tenant ERP architecture, policy-driven business engines, and APIs for web, mobile and desktop apps.',
                'og_image' => null,
            ],

            'is_available' => true,
        ]);
    }
}
