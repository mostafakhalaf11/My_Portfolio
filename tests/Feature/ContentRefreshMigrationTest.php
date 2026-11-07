<?php

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use App\Models\Profile;
use Database\Seeders\ProfileSeeder;

/** Runs the content migration again against rows shaped like an older production database. */
function runContentRefresh(): void
{
    (require database_path('migrations/2026_11_07_153330_refresh_portfolio_content_2026_11.php'))->up();
}

it('updates fields that still hold their originally seeded value', function () {
    // 'Architect and developer' is the role exactly as first seeded in production.
    CaseStudy::create([
        'slug' => 'stock-yard-commerce-rfq',
        'title' => 'Old title',
        'summary' => 's',
        'role' => 'Architect and developer',
        'status' => ContentStatus::Published,
    ]);

    runContentRefresh();

    expect(CaseStudy::firstWhere('slug', 'stock-yard-commerce-rfq')->role)->toBe('Sole architect and developer');
});

it('never overwrites a field that was edited in the admin panel', function () {
    CaseStudy::create([
        'slug' => 'stock-yard-commerce-rfq',
        'title' => 'Edited in /admin',
        'summary' => 's',
        'role' => 'Edited in /admin',
        'status' => ContentStatus::Published,
    ]);

    runContentRefresh();

    $study = CaseStudy::firstWhere('slug', 'stock-yard-commerce-rfq');

    expect($study->title)->toBe('Edited in /admin')
        ->and($study->role)->toBe('Edited in /admin');
});

it('gives an existing profile the photo without replacing one already set', function () {
    $this->seed(ProfileSeeder::class);
    Profile::query()->update(['avatar_path' => null]);

    runContentRefresh();
    expect(Profile::current()->avatar_path)->toBe('images/mostafa-khalaf.webp');

    Profile::query()->update(['avatar_path' => 'images/other.webp']);
    runContentRefresh();
    expect(Profile::current()->avatar_path)->toBe('images/other.webp');
});

it('does nothing on a fresh database', function () {
    runContentRefresh();

    expect(CaseStudy::count())->toBe(0);
});
