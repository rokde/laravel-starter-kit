<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('a workspace owner can remove a member', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Create a member to remove
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'member']);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete("/workspaces/current/members/{$member->id}");

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Member removed.');

    // Check that the member was removed from the workspace
    $this->assertDatabaseMissing('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $member->id,
    ]);
});

test('an admin can remove a member', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create an admin user
    $admin = User::factory()->create();
    $workspace->users()->attach($admin, ['role' => 'admin']);
    $admin->switchWorkspace($workspace);

    // Create a member to remove
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'editor']);

    // Act
    $response = $this
        ->actingAs($admin)
        ->delete("/workspaces/current/members/{$member->id}");

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Member removed.');

    // Check that the member was removed from the workspace
    $this->assertDatabaseMissing('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $member->id,
    ]);
});

test('a regular member cannot remove another member', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create a regular member
    $member1 = User::factory()->create();
    $workspace->users()->attach($member1, ['role' => 'member']);
    $member1->switchWorkspace($workspace);

    // Create another member
    $member2 = User::factory()->create();
    $workspace->users()->attach($member2, ['role' => 'member']);

    // Act
    $response = $this
        ->actingAs($member1)
        ->delete("/workspaces/current/members/{$member2->id}");

    // Assert
    $response->assertForbidden();

    // Check that the member was not removed from the workspace
    $this->assertDatabaseHas('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $member2->id,
    ]);
});

test('a user cannot remove a member from a workspace they do not belong to', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create a member in the workspace
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'admin']);

    // Create a user who doesn't belong to the workspace
    $otherUser = User::factory()->create();
    $otherWorkspace = Workspace::factory()->create(['user_id' => $otherUser->id]);
    $otherUser->switchWorkspace($otherWorkspace);

    // Act
    $response = $this
        ->actingAs($otherUser)
        ->delete("/workspaces/current/members/{$member->id}");

    // Assert
    $response->assertForbidden();

    // Check that the member was not removed from the workspace
    $this->assertDatabaseHas('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $member->id,
    ]);
});

test('a user cannot remove the workspace owner', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create an admin user
    $admin = User::factory()->create();
    $workspace->users()->attach($admin, ['role' => 'admin']);
    $admin->switchWorkspace($workspace);

    // Act
    $response = $this
        ->actingAs($admin)
        ->delete("/workspaces/current/members/{$owner->id}");

    // Assert
    $response->assertForbidden();

    // Check that the owner was not removed from the workspace
    $this->assertDatabaseHas('workspaces', [
        'id' => $workspace->id,
        'user_id' => $owner->id,
    ]);
});

test('a user cannot remove a non-existent member', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete('/workspaces/current/members/999999');

    // Assert
    $response->assertNotFound();
});

test('removing a member updates their current workspace if needed', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    // Create a member and set the workspace as their current workspace
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'member']);
    $member->switchWorkspace($workspace);

    // Verify the member's current workspace is set
    $this->assertEquals($workspace->id, $member->fresh()->workspace_id);

    // Act
    $response = $this
        ->actingAs($owner)
        ->delete("/workspaces/current/members/{$member->id}");

    // Assert
    $response->assertRedirect();

    // Check that the member was removed from the workspace
    $this->assertDatabaseMissing('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $member->id,
    ]);

    // Check that the member's current workspace was reset to null
    $this->assertNull($member->fresh()->workspace_id);
});
