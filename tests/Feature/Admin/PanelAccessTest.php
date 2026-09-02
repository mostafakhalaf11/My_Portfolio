<?php

use App\Models\User;

it('lets an allowlisted address into the panel', function () {
    $admin = User::factory()->create(['email' => 'mostafkhalaf11@gmail.com']);

    expect($admin->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();

    $this->actingAs($admin)->get('/admin')->assertSuccessful();
});

it('matches the allowlist case-insensitively', function () {
    $admin = User::factory()->create(['email' => 'MostafKhalaf11@Gmail.com']);

    expect($admin->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

it('keeps a user who is not on the allowlist out of the panel', function () {
    $intruder = User::factory()->create(['email' => 'someone-else@example.com']);

    expect($intruder->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();

    $this->actingAs($intruder)->get('/admin')->assertForbidden();
});

it('sends guests to the login screen', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});
