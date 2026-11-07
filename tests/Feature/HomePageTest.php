<?php

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use App\Models\Profile;
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

it('keeps the homepage introduction and current role highlights focused', function () {
    $this->get('/')
        ->assertInertia(fn (Assert $page) => $page
            ->where('sections.hero.heading', 'I build the software businesses run on.')
            ->where('sections.hero.subheading', 'Full-stack software engineer turning complex business rules — payroll, permissions, pricing — into reliable, multi-tenant products.')
            ->where('profile.avatarUrl', asset('images/mostafa-khalaf.webp'))
            ->has('experiences.0.highlights', 4)
            ->where('caseStudies.1.clientLabel', 'Freelance')
            ->where('caseStudies.1.role', 'Sole architect and developer')
        );
});

it('keeps architecture diagrams on project pages, out of the home page', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page->missing('caseStudies.0.diagram'));

    $this->get('/projects/amg-erp-multi-tenant-hrm-payroll')
        ->assertInertia(fn (Assert $page) => $page
            ->has('study.diagram.nodes', 9)
            ->has('study.diagram.edges', 8)
        );
});

it('caps homepage outcome previews at three per card', function () {
    $this->get('/')
        ->assertInertia(fn (Assert $page) => $page->has('caseStudies.0.outcomes', 3));
});

it('renders a project page with its full narrative', function () {
    $this->get('/projects/amg-erp-multi-tenant-hrm-payroll')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('CaseStudies/Show')
            ->where('study.title', 'AMG ERP — Multi-Tenant HRM and Payroll Platform')
            ->where('study.years', '2025–present')
            ->has('study.optionsRejected', 3)
            ->has('study.outcomes', 5)
            ->where('study.optionsRejected.0.option', 'Database per tenant')
            ->where('study.summary', 'Modernized a single-tenant ERP into a modular, company-scoped platform for attendance, leave, payroll, and employee apps.')
            ->whereNot('study.context', '')
            ->whereNot('study.decision', '')
        );
});

it('permanently redirects the old case study URLs to the projects section', function () {
    $this->get('/case-studies/amg-erp-multi-tenant-hrm-payroll')
        ->assertStatus(301)
        ->assertRedirect('/projects/amg-erp-multi-tenant-hrm-payroll');
});

it('exposes each project stage and its roadmap', function () {
    $this->get('/projects/amg-erp-multi-tenant-hrm-payroll')
        ->assertInertia(fn (Assert $page) => $page
            ->where('study.stage', ['value' => 'live', 'label' => 'Live in production'])
            ->has('study.roadmap', 5)
        );

    $this->get('/')
        ->assertInertia(fn (Assert $page) => $page
            ->where('caseStudies.0.stage.value', 'live')
            ->where('caseStudies.1.stage.value', 'staging')
        );
});

it('links Stock Yard only to pages a visitor can actually open', function () {
    // The repository is private and staging sits behind Cloudflare Access.
    $this->get('/projects/stock-yard-commerce-rfq')
        ->assertInertia(fn (Assert $page) => $page
            ->where('study.liveUrl', 'https://stock-yard.com')
            ->where('study.repoUrl', null)
            ->where('study.stage.label', 'Private staging')
        );
});

it('hides the CV button until the PDF exists', function () {
    // Point at a file that never exists, so the test holds whether or not the real CV is in public/cv.
    Profile::current()->update(['resume_path' => 'cv/not-uploaded-yet.pdf']);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('profile.resumeUrl', null));

    Profile::current()->update(['resume_path' => 'favicon.ico']);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('profile.resumeUrl', asset('favicon.ico')));
});

it('passes the hero "now" line through from the CMS', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page->has('sections.hero.settings.now'));
});

it('returns 404 for a draft case study', function () {
    CaseStudy::create([
        'slug' => 'unreleased',
        'title' => 'Unreleased',
        'summary' => 'Not for the public yet.',
        'status' => ContentStatus::Draft,
    ]);

    $this->get('/projects/unreleased')->assertNotFound();
});

it('returns 404 for a case study published in the future', function () {
    CaseStudy::create([
        'slug' => 'scheduled',
        'title' => 'Scheduled',
        'summary' => 'Goes live later.',
        'status' => ContentStatus::Published,
        'published_at' => now()->addWeek(),
    ]);

    $this->get('/projects/scheduled')->assertNotFound();
});
