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
            'headline' => 'Backend Software Engineer',
            'title' => 'Laravel | PHP | Multi-Tenant SaaS',
            'summary' => 'Backend Software Engineer with 2 years of experience building multi-tenant SaaS platforms in Laravel and PHP. Lead engineer on a modular HRM/Payroll ERP, owning its tenancy architecture, attendance and payroll engines and authorization layers, and sole architect of a bilingual commerce platform on Laravel 13 with a Nuxt 4 storefront. Strong in domain modeling, policy-driven business engines, data migration under production constraints, and REST API design that stays maintainable as systems grow. Comfortable owning delivery from schema design through Dockerized deployment and CI/CD.',

            'phone' => '+20 115 347 0446',
            'phone_link' => 'tel:+201153470446',
            'location' => 'Cairo, Egypt',
            'github_url' => 'https://github.com/mostafakhalaf11',
            'linkedin_url' => 'https://www.linkedin.com/in/mostafa-khalaf-3b003621a/',

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
                'title' => 'Mostafa Khalaf Mohamed — Backend Software Engineer',
                'description' => 'Backend engineer building multi-tenant SaaS platforms in Laravel and PHP. Multi-tenant ERP architecture, policy-driven business engines, and API design.',
                'og_image' => null,
            ],

            'is_available' => true,
        ]);
    }
}
