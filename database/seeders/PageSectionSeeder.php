<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    /** Public so content migrations can apply the same copy to databases seeded earlier. */
    public const SECTIONS = [
        [
            'key' => 'hero',
            // Outcome-led, framework-free: the stack lives in the Stack section and project chips.
            'heading' => 'I build the software businesses run on.',
            'subheading' => 'Full-stack software engineer turning complex business rules — payroll, permissions, pricing — into reliable, multi-tenant products.',
            'settings' => [
                'show_availability' => true,
                // Capabilities, not figures from a specific project or employer.
                'proof' => 'Multi-tenant architecture · Business-rule engines · Zero-data-loss migrations · Bilingual products',
                'now' => 'Building the API for AMG ERP mobile and desktop apps; preparing Stock Yard for launch.',
            ],
        ],
        [
            // Key stays `case_studies` (it is internal); the public name is "Featured projects".
            'key' => 'case_studies',
            'heading' => 'Featured projects',
            'subheading' => 'Selected results, key trade-offs, and how the systems work.',
            'settings' => ['limit' => 4],
        ],
        [
            'key' => 'stack',
            'heading' => 'Technical stack',
            'subheading' => 'What I reach for, grouped by where it sits in the system.',
            'settings' => ['primary_only' => true],
        ],
        [
            'key' => 'experience',
            'heading' => 'Experience',
            'subheading' => null,
            'settings' => ['show_tech' => true],
        ],
        [
            'key' => 'contact',
            'heading' => 'Get in touch',
            'subheading' => 'Open to full-stack and backend roles, remote or Cairo-based.',
            'settings' => ['show_form' => true],
        ],
    ];

    public function run(): void
    {
        foreach (self::SECTIONS as $index => $section) {
            PageSection::updateOrCreate(
                ['page' => 'home', 'key' => $section['key']],
                [...$section, 'sort_order' => $index, 'is_visible' => true],
            );
        }
    }
}
