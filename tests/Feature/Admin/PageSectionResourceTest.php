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
        ->assertSchemaStateSet(['settings.show_availability' => true])
        ->fillForm([
            'heading' => 'New headline',
            'settings.show_availability' => false,
            'settings.proof' => 'Shipped a thing',
            'settings.now' => 'Shipping the next thing',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    // MySQL's JSON type reorders keys, so compare sorted; toBe() keeps the booleans strict.
    expect($hero->fresh()->heading)->toBe('New headline')
        ->and(collect($hero->fresh()->settings)->sortKeys()->all())->toBe([
            'now' => 'Shipping the next thing',
            'proof' => 'Shipped a thing',
            'show_availability' => false,
        ]);
});

it('edits the stack primary-only setting through a toggle', function () {
    $stack = PageSection::where('key', 'stack')->firstOrFail();

    Livewire::test(EditPageSection::class, ['record' => $stack->getRouteKey()])
        ->assertSchemaStateSet(['settings.primary_only' => true])
        ->fillForm(['settings.primary_only' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($stack->fresh()->settings)->toBe(['primary_only' => false]);
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
