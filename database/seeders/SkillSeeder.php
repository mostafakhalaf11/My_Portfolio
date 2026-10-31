<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Groups and their skills, in resume order.
     * `primary` marks what leads the homepage stack section.
     */
    private const GROUPS = [
        [
            'name' => 'Backend & Architecture',
            'icon' => 'heroicon-o-server-stack',
            'description' => 'Where most of the work lives: domain modeling, business engines, and APIs that stay maintainable.',
            'primary' => ['PHP 8.1-8.5', 'Laravel 10/13', 'Filament', 'RESTful APIs', 'Multi-tenancy', 'RBAC and ABAC authorization'],
            'skills' => [
                'PHP 8.1-8.5', 'Laravel 10/13', 'Filament', 'RESTful APIs', 'OpenAPI', 'Laravel Sanctum',
                'OOP', 'SOLID', 'Design patterns (strategy, factory, state machine)',
                'Modular monolith and domain-driven design', 'Service/action layers', 'DTOs',
                'Events and listeners', 'Queues, jobs and schedulers', 'Multi-tenancy',
                'RBAC and ABAC authorization', 'Policy-driven rule engines',
            ],
        ],
        [
            'name' => 'Data & Infrastructure',
            'icon' => 'heroicon-o-circle-stack',
            'description' => 'Schema design, migrations under production constraints, and owning the path to deployment.',
            'primary' => ['MySQL 8', 'Redis', 'Docker and Docker Compose', 'GitHub Actions CI/CD'],
            'skills' => [
                'MySQL 8', 'Redis', 'Eloquent ORM', 'Query optimization and indexing',
                'Schema and data migrations', 'Backfill and reconciliation strategies',
                'Docker and Docker Compose', 'GitHub Actions CI/CD', 'Nginx',
                'Linux VPS (OVH, Hostinger)', 'Cloudflare', 'SSH',
                'Deployment and release management', 'Backup and restore',
            ],
        ],
        [
            'name' => 'Testing & Quality',
            'icon' => 'heroicon-o-beaker',
            'description' => 'Static analysis and contract validation wired into CI, not run by hand.',
            'primary' => ['PHPUnit', 'PHPStan/Larastan static analysis'],
            'skills' => [
                'PHPUnit', 'PHPStan/Larastan static analysis', 'Laravel Pint', 'Code review',
                'Git and GitHub', 'Technical documentation', 'API contract validation in CI',
            ],
        ],
        [
            'name' => 'Frontend',
            'icon' => 'heroicon-o-window',
            'description' => 'Server-rendered storefronts and admin interfaces, shipped end to end — including Arabic RTL.',
            'primary' => ['Nuxt 4', 'Vue 3', 'TypeScript', 'TailwindCSS'],
            'skills' => [
                'Nuxt 4', 'Vue 3', 'TypeScript', 'Blade', 'Alpine.js', 'TailwindCSS',
                'JavaScript', 'HTML5', 'CSS3', 'i18n and RTL localization', 'DataTables', 'Figma',
            ],
        ],
        [
            'name' => 'AI-Assisted Engineering',
            'icon' => 'heroicon-o-cpu-chip',
            'description' => 'Agent tooling and specification-first delivery as part of the daily loop.',
            'primary' => ['Claude Code', 'MCP server integration'],
            'skills' => [
                'Claude Code', 'Custom agent skills and project conventions',
                'MCP server integration', 'Specification-driven delivery in Notion',
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::GROUPS as $groupIndex => $group) {
            $model = SkillGroup::updateOrCreate(
                ['name' => $group['name']],
                [
                    'icon' => $group['icon'],
                    'description' => $group['description'],
                    'sort_order' => $groupIndex,
                ],
            );

            foreach ($group['skills'] as $skillIndex => $name) {
                Skill::updateOrCreate(
                    ['skill_group_id' => $model->id, 'name' => $name],
                    [
                        'is_primary' => in_array($name, $group['primary'], true),
                        'sort_order' => $skillIndex,
                    ],
                );
            }
        }
    }
}
