<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

uses(RefreshDatabase::class);

test('a workspace owner can revoke an invitation', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Create an invitation
    $invitation = WorkspaceInvitation::create([
        'workspace_id' => $workspace->id,
        'email' => 'test@example.com',
        'role' => 'member',
    ]);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete("/workspaces/current/invitations/{$invitation->id}");

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Team invitation revoked.');

    // Check that the invitation was deleted
    $this->assertDatabaseMissing('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('an admin can revoke an invitation', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create an admin user
    $admin = User::factory()->create();
    $workspace->users()->attach($admin, ['role' => 'admin']);
    $admin->switchWorkspace($workspace);

    // Create an invitation
    $invitation = WorkspaceInvitation::create([
        'workspace_id' => $workspace->id,
        'email' => 'test@example.com',
        'role' => 'member',
    ]);

    // Act
    $response = $this
        ->actingAs($admin)
        ->delete("/workspaces/current/invitations/{$invitation->id}");

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Team invitation revoked.');

    // Check that the invitation was deleted
    $this->assertDatabaseMissing('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a regular member cannot revoke an invitation', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create a regular member
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'member']);
    $member->switchWorkspace($workspace);

    // Create an invitation
    $invitation = WorkspaceInvitation::create([
        'workspace_id' => $workspace->id,
        'email' => 'test@example.com',
        'role' => 'member',
    ]);

    // Act
    $response = $this
        ->actingAs($member)
        ->delete("/workspaces/current/invitations/{$invitation->id}");

    // Assert
    $response->assertForbidden();

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a user cannot revoke an invitation for a workspace they do not belong to', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create an invitation
    $invitation = WorkspaceInvitation::create([
        'workspace_id' => $workspace->id,
        'email' => 'test@example.com',
        'role' => 'member',
    ]);

    // Create a user who doesn't belong to the workspace
    $otherUser = User::factory()->create();

    // Act
    $response = $this
        ->actingAs($otherUser)
        ->delete("/workspaces/current/invitations/{$invitation->id}");

    // Assert
    $response->assertForbidden();

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a user cannot revoke an invitation to the workspace owner', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Create an invitation to the owner's email
    $invitation = WorkspaceInvitation::create([
        'workspace_id' => $workspace->id,
        'email' => $owner->email,
        'role' => 'member', // This would be strange in practice, but we're testing the policy
    ]);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete("/workspaces/current/invitations/{$invitation->id}");

    // Assert
    $response->assertForbidden();

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a user cannot revoke a non-existent invitation', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete('/workspaces/invitations/999999');

    // Assert
    $response->assertNotFound();
});
