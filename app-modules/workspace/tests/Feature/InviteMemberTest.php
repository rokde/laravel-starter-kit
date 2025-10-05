<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

uses(RefreshDatabase::class);

test('workspace member page is displayed', function (): void {
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

    $this
        ->actingAs($user)
        ->get('/workspaces/current/invitations')
        ->assertOk();
});

test('workspace member can access invitation page', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Create a regular member
    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => current(RoleRegistry::roleKeys())]);
    $member->switchWorkspace($workspace);

    $this
        ->actingAs($member)
        ->get('/workspaces/current/invitations')
        ->assertOk();
});

test('a user can be invited to a workspace', function (): void {
    $user = User::factory()->create();
    $user->switchWorkspace(Workspace::factory()->create(['user_id' => $user->id]));

    $email = fake()->safeEmail();
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

test('a user can accept an invitation', function (): void {
    Mail::fake();

    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    $invitedUser = User::factory()->create();
    $email = $invitedUser->email;
    $role = current(RoleRegistry::roleKeys());

    // Create invitation
    /** @var WorkspaceInvitation $invitation */
    $invitation = $workspace->invitations()->create([
        'email' => $email,
        'role' => $role,
    ]);

    $this->assertDatabaseMissing('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $invitedUser->id,
    ]);

    $this
        ->actingAs($invitedUser)
        ->get($invitation->getAcceptUrl())
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('message', 'You have been added to the workspace.');

    // Check that the user is now a member of the workspace
    $this->assertDatabaseHas('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $invitedUser->id,
        'role' => $role,
    ]);

    // Check that the invitation was deleted
    $this->assertDatabaseMissing('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);

    $this->assertTrue($invitedUser->fresh()->hasWorkspaceRole($workspace, $role));
});

test('a user cannot accept an invitation with an invalid signature', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    $invitedUser = User::factory()->create();
    $email = $invitedUser->email;
    $role = current(RoleRegistry::roleKeys());

    // Create invitation
    $invitation = WorkspaceInvitation::query()->create([
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    $invalidUrl = route('public.api.invitations.accept', [
        'invitation' => $invitation->id,
    ]);

    // Act
    $response = $this
        ->actingAs($invitedUser)
        ->get($invalidUrl);

    // Assert
    $response->assertForbidden();

    // Check that the user is not a member of the workspace
    $this->assertDatabaseMissing('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $invitedUser->id,
    ]);

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a guest is redirected to login when accepting an invitation', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Create invitation
    $invitation = WorkspaceInvitation::query()->create([
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    $acceptUrl = Illuminate\Support\Facades\URL::signedRoute('public.api.invitations.accept', [
        'invitation' => $invitation->id,
    ]);

    // Act
    $response = $this->get($acceptUrl);

    // Assert
    $response->assertRedirect(route('signup-or-login-to-accept-invitation'));

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('a workspace owner cannot accept an invitation to their own workspace', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    $email = $owner->email;
    $role = 'member'; // Different role than owner

    // Create invitation
    $invitation = WorkspaceInvitation::query()->create([
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    $acceptUrl = Illuminate\Support\Facades\URL::signedRoute('public.api.invitations.accept', [
        'invitation' => $invitation->id,
    ]);

    // Act
    $response = $this
        ->actingAs($owner)
        ->get($acceptUrl);

    // Assert
    $response->assertForbidden();

    // Check that the invitation still exists
    $this->assertDatabaseHas('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});

test('an invitation can be accepted by a user with a different email', function (): void {
    // Arrange
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    $email = 'invited@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Create invitation
    $invitation = WorkspaceInvitation::query()->create([
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    // User with different email
    $user = User::factory()->create(['email' => 'different@example.com']);

    $acceptUrl = Illuminate\Support\Facades\URL::signedRoute('public.api.invitations.accept', [
        'invitation' => $invitation->id,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->get($acceptUrl);

    // Assert
    $response->assertRedirect(route('dashboard'));

    // Check that the user is now a member of the workspace
    $this->assertDatabaseHas('workspace_members', [
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'role' => $role,
    ]);

    // Check that the invitation was deleted
    $this->assertDatabaseMissing('workspace_member_invitations', [
        'id' => $invitation->id,
    ]);
});
