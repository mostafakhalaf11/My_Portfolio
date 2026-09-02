<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    private const SECTIONS = [
        [
            'key' => 'hero',
            'heading' => 'I build the parts of the system nobody sees.',
            'subheading' => 'Multi-tenant architecture, policy-driven business engines, and APIs that survive their second year.',
            'settings' => ['show_diagram' => true, 'show_availability' => true],
        ],
        [
            'key' => 'case_studies',
            'heading' => 'Case studies',
            'subheading' => 'Context, constraints, the options rejected, and what the decision measured out to.',
            'settings' => ['limit' => 4],
        ],
        [
            'key' => 'stack',
            'heading' => 'Technical stack',
            'subheading' => 'What I reach for, grouped by where it sits in the system.',
            'settings' => ['primary_only' => false],
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
            'subheading' => 'Open to backend and full-stack roles, remote or Cairo-based.',
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
