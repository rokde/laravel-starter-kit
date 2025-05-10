<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('owned workspaces can be switched', function (): void {
    $this->actingAs($user = User::factory()->create());

    $this->post('/workspaces', [
        'name' => 'Test Workspace 1',
    ]);
    $this->post('/workspaces', [
        'name' => 'Test Workspace 2',
    ]);

    $workspace1 = Workspace::query()->where('name', 'Test Workspace 1')->first();
    $workspace2 = Workspace::query()->where('name', 'Test Workspace 2')->first();

    $this->put('/workspaces/current', [
        'workspace_id' => $workspace2->id,
    ]);

    $this->assertDatabaseHas('users', [
        'workspace_id' => $workspace2->id,
    ]);

    $this->put('/workspaces/current', [
        'workspace_id' => $workspace1->id,
    ]);

    $this->assertDatabaseHas('users', [
        'workspace_id' => $workspace1->id,
    ]);
});
