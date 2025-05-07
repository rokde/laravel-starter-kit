<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('locale settings page is displayed', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/locale');

    $response->assertOk();
});

test('locale settings can be updated', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/settings/locale', [
            'locale' => 'de',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/locale');

    $user->refresh();

    expect($user->preferredLocale())->toBe('de');
});
