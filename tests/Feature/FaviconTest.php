<?php

use Database\Seeders\DatabaseSeeder;

it('ships real icon files, not empty placeholders', function (string $file) {
    // The skeleton's favicon.ico was 0 bytes, which every browser renders as a blank globe.
    expect(public_path($file))->toBeFile()
        ->and(filesize(public_path($file)))->toBeGreaterThan(0);
})->with(['favicon.svg', 'favicon.ico', 'apple-touch-icon.png', 'admin-favicon.svg']);

it('links the portfolio icons from the public layout', function () {
    $this->seed(DatabaseSeeder::class);

    // The <head> is server-rendered Blade, so asserting on HTML is meaningful here.
    $this->get('/')
        ->assertSee('favicon.svg', false)
        ->assertSee('apple-touch-icon.png', false)
        ->assertDontSee('admin-favicon.svg', false);
});

it('gives the admin panel its own icon', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('admin-favicon.svg', false);
});
