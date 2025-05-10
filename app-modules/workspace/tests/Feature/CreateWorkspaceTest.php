<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('workspaces can be created', function (): void {
    $this->actingAs($user = User::factory()->create());

    $this->post('/workspaces', [
        'name' => 'Test Workspace',
    ]);

    $this->assertCount(1, $user->fresh()->ownedWorkspaces);
    $this->assertEquals('Test Workspace', $user->fresh()->ownedWorkspaces()->latest('id')->first()->name);
});
