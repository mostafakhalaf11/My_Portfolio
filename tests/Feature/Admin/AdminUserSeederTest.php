<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Hash;

it('seeds an admin who can sign in and reach the panel', function () {
    config()->set('portfolio.admin_password', 'seeder-test-secret');

    $this->seed(AdminUserSeeder::class);

    $admin = User::firstWhere('email', config('portfolio.admin_emails')[0]);

    expect($admin)->not->toBeNull()
        ->and(Hash::check('seeder-test-secret', $admin->password))->toBeTrue()
        ->and($admin->password)->not->toBe('seeder-test-secret')
        ->and($admin->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

it('is idempotent and never duplicates the admin', function () {
    config()->set('portfolio.admin_password', 'seeder-test-secret');

    $this->seed(AdminUserSeeder::class);
    $this->seed(AdminUserSeeder::class);

    expect(User::where('email', config('portfolio.admin_emails')[0])->count())->toBe(1);
});

it('creates nothing when no password is configured', function () {
    config()->set('portfolio.admin_password', null);

    $this->seed(AdminUserSeeder::class);

    expect(User::count())->toBe(0);
});

it('keeps credentials out of version-controlled source', function () {
    // Guards against a future edit hardcoding the real password back into the repo.
    $tracked = ['app', 'config', 'database', 'routes', 'tests', 'resources'];
    $password = env('PORTFOLIO_ADMIN_PASSWORD');
    // Built at runtime so this assertion does not match its own source file.
    $assignment = 'PORTFOLIO_ADMIN_PASSWORD'.'=';

    foreach ($tracked as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir)));

        foreach ($files as $file) {
            if (! $file->isFile() || ! in_array($file->getExtension(), ['php', 'vue', 'js', 'blade'], true)) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            expect($contents)->not->toContain($assignment);

            if (filled($password)) {
                expect($contents)->not->toContain($password);
            }
        }
    }
});
