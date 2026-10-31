<?php

use App\Filament\Resources\PageSections\Pages\EditPageSection;
use App\Filament\Resources\PageSections\Pages\ListPageSections;
use App\Models\PageSection;
use App\Models\User;
use Database\Seeders\PageSectionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(PageSectionSeeder::class);
    $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_emails')[0]]));
});

it('renders the page section list', function () {
    Livewire::test(ListPageSections::class)->assertOk();
});

it('edits the hero settings through typed fields', function () {
    $hero = PageSection::where('key', 'hero')->firstOrFail();

    Livewire::test(EditPageSection::class, ['record' => $hero->getRouteKey()])
        ->assertSchemaStateSet(['settings.show_diagram' => true, 'settings.show_availability' => true])
        ->fillForm([
            'heading' => 'New headline',
            'settings.show_diagram' => false,
            'settings.now' => 'Shipping the next thing',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    // MySQL's JSON type reorders keys, so compare sorted; toBe() keeps the booleans strict.
    expect($hero->fresh()->heading)->toBe('New headline')
        ->and(collect($hero->fresh()->settings)->sortKeys()->all())->toBe([
            'now' => 'Shipping the next thing',
            'show_availability' => true,
            'show_diagram' => false,
        ]);
});

it('keeps the stored settings of blocks without a settings form', function () {
    $contact = PageSection::where('key', 'contact')->firstOrFail();

    Livewire::test(EditPageSection::class, ['record' => $contact->getRouteKey()])
        ->fillForm(['subheading' => 'Open to new roles.'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contact->fresh()->subheading)->toBe('Open to new roles.')
        ->and($contact->fresh()->settings)->toBe(['show_form' => true])
        ->and($contact->fresh()->key)->toBe('contact');
});

it('compiles views under the path the runtime resolves', function () {
    // Guards the Windows fix: view:cache hashes realpath(), the runtime hashes "<hint>/<name>".
    $path = app('view')->getFinder()->find('filament-panels::livewire.topbar');
    $compiled = app('blade.compiler')->getCompiledPath($path);

    @unlink($compiled);

    $this->artisan('views:warm')->assertSuccessful();

    expect($compiled)->toBeFile();
});
