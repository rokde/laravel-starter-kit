<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('workspace page is displayed', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $response = $this
        ->actingAs($user)
        ->get('/workspaces/current');

    $response->assertOk();
});

test('workspaces can be updated', function (): void {
    $this->actingAs($user = User::factory()->create());
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $this->patch('/workspaces/current', [
        'name' => 'Updated Test Workspace',
    ]);
    $this->assertDatabaseHas('workspaces', [
        'name' => 'Updated Test Workspace',
    ]);
});
