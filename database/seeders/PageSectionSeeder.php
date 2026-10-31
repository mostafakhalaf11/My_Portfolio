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
            'settings' => [
                'show_diagram' => true,
                'show_availability' => true,
                'now' => 'Shipping the API behind the AMG ERP mobile and desktop apps · Stock Yard in private staging ahead of launch',
            ],
        ],
        [
            // Key stays `case_studies` (it is internal); the public name is "Featured projects".
            'key' => 'case_studies',
            'heading' => 'Featured projects',
            'subheading' => 'Production systems I designed and shipped — the context, the options rejected, and what each decision measured out to.',
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
