<?php

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders the home page with every seeded section', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('profile.name', 'Mostafa Khalaf Mohamed')
            ->where('profile.isAvailable', true)
            ->has('profile.languages', 3)
            ->has('sections.hero')
            ->has('sections.case_studies')
            ->has('sections.stack')
            ->has('sections.experience')
            ->has('sections.contact')
            ->has('caseStudies', 2)
            ->has('skillGroups', 5)
            ->has('experiences', 4)
            ->has('certifications', 3)
            ->has('education', 1)
        );
});

it('exposes the hero diagram data the SVG renders from', function () {
    $this->get('/')
        ->assertInertia(fn (Assert $page) => $page
            ->has('caseStudies.0.diagram.nodes', 9)
            ->has('caseStudies.0.diagram.edges', 8)
        );
});

it('caps homepage outcome previews at three per card', function () {
    $this->get('/')
        ->assertInertia(fn (Assert $page) => $page->has('caseStudies.0.outcomes', 3));
});

it('renders a case study page with its full narrative', function () {
    $this->get('/case-studies/amg-erp-multi-tenant-hrm-payroll')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CaseStudies/Show')
            ->where('study.title', 'AMG ERP — Multi-Tenant HRM and Payroll Platform')
            ->has('study.optionsRejected', 3)
            ->has('study.outcomes', 5)
            ->where('study.optionsRejected.0.option', 'Database per tenant')
            ->whereNot('study.context', '')
            ->whereNot('study.decision', '')
        );
});

it('returns 404 for a draft case study', function () {
    CaseStudy::create([
        'slug' => 'unreleased',
        'title' => 'Unreleased',
        'summary' => 'Not for the public yet.',
        'status' => ContentStatus::Draft,
    ]);

    $this->get('/case-studies/unreleased')->assertNotFound();
});

it('returns 404 for a case study published in the future', function () {
    CaseStudy::create([
        'slug' => 'scheduled',
        'title' => 'Scheduled',
        'summary' => 'Goes live later.',
        'status' => ContentStatus::Published,
        'published_at' => now()->addWeek(),
    ]);

    $this->get('/case-studies/scheduled')->assertNotFound();
});
