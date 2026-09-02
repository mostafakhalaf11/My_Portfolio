<?php

use App\Enums\ContentStatus;
use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\ListCaseStudies;
use App\Models\CaseStudy;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Panel access is allowlisted by email, so the test admin must match.
    $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_emails')[0]]));
});

it('renders the case study list', function () {
    Livewire::test(ListCaseStudies::class)->assertOk();
});

it('renders the rewritten case study form', function () {
    Livewire::test(CreateCaseStudy::class)->assertOk();
});

it('creates a case study with structured narrative fields', function () {
    Livewire::test(CreateCaseStudy::class)
        ->fillForm([
            'title' => 'Multi-tenant migration',
            'slug' => 'multi-tenant-migration',
            'summary' => 'Moved 40 global tables to company-scoped ownership.',
            'options_rejected' => [
                ['option' => 'Database per tenant', 'why_rejected' => 'Migration cost per release.'],
            ],
            'outcomes' => [
                ['metric' => 'Tables migrated', 'value' => '~40', 'note' => 'zero data loss'],
            ],
            'status' => ContentStatus::Published->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $study = CaseStudy::firstWhere('slug', 'multi-tenant-migration');

    expect($study)->not->toBeNull()
        ->and($study->options_rejected)->toHaveCount(1)
        ->and($study->outcomes[0]['value'])->toBe('~40')
        ->and($study->isPublished())->toBeTrue();
});

it('scopes drafts out of the published query', function () {
    CaseStudy::create(['slug' => 'draft-one', 'title' => 'Draft', 'summary' => 's', 'status' => ContentStatus::Draft]);
    CaseStudy::create(['slug' => 'live-one', 'title' => 'Live', 'summary' => 's', 'status' => ContentStatus::Published]);

    expect(CaseStudy::published()->pluck('slug')->all())->toBe(['live-one']);
});
