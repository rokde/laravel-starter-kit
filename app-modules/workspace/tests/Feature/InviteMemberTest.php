<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('workspace team page is displayed', function (): void {
    $user = User::factory()->create();
    $user->switchWorkspace(Workspace::factory()->create(['user_id' => $user->id]));

    $response = $this
        ->actingAs($user)
        ->get('/workspaces/current/members');

    $response->assertOk();
});

test('workspace invitation page is displayed', function (): void {
    $user = User::factory()->create();
    $user->switchWorkspace(Workspace::factory()->create(['user_id' => $user->id]));

    $response = $this
        ->actingAs($user)
        ->get('/workspaces/current/invitations');

    $response->assertOk();
});

test('a user can be invited to a workspace', function (): void {
    $user = User::factory()->create();
    $user->switchWorkspace(Workspace::factory()->create(['user_id' => $user->id]));

    $email = fake()->safeEmail;
    $role = current(RoleRegistry::roleKeys());
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('workspace_member_invitations', [
        'email' => $email,
        'role' => $role,
    ]);
});
