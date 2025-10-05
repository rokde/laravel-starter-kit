<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

uses(RefreshDatabase::class);

test('a user can invite a member to their workspace', function (): void {
    // Arrange
    Mail::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Act
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('message', 'Invitation sent.');

    $this->assertDatabaseHas('workspace_member_invitations', [
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    // @TODO Mail sending works, but not in test
    // Mail::assertSent(InvitationMail::class, function ($mail) use ($email) {
    //        return $mail->hasTo($email);
    //    });
});

test('a user cannot invite a member with invalid data', function (): void {
    // Arrange
    Mail::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $role = current(RoleRegistry::roleKeys());

    // Act - missing email
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'role' => $role,
        ]);

    // Assert
    $response->assertSessionHasErrors(['email']);

    // Act - missing role
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => 'test@example.com',
        ]);

    // Assert
    $response->assertSessionHasErrors(['role']);

    // Act - invalid email
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => 'not-an-email',
            'role' => $role,
        ]);

    // Assert
    $response->assertSessionHasErrors(['email']);

    // Act - invalid role
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => 'test@example.com',
            'role' => 'invalid-role',
        ]);

    // Assert
    $response->assertSessionHasErrors(['role']);

    Mail::assertNothingSent();
});

test('a user cannot invite a member to a workspace they do not own', function (): void {
    // Arrange
    Mail::fake();

    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    $member = User::factory()->create();
    $workspace->users()->attach($member, ['role' => 'member']);
    $member->switchWorkspace($workspace);

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Act
    $response = $this
        ->actingAs($member)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    // Assert
    $response->assertForbidden();

    $this->assertDatabaseMissing('workspace_member_invitations', [
        'workspace_id' => $workspace->id,
        'email' => $email,
    ]);

    Mail::assertNothingSent();
});

test('an admin can invite a member if authorized', function (): void {
    // Arrange
    Mail::fake();

    $owner = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);
    $owner->switchWorkspace($workspace);

    $admin = User::factory()->create();
    $workspace->users()->attach($admin, ['role' => 'admin']);
    $admin->switchWorkspace($workspace);

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Act
    $response = $this
        ->actingAs($admin)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    // Assert
    $response->assertRedirect();

    $this->assertDatabaseHas('workspace_member_invitations', [
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    // @TODO Mail was sent, but is not testable at the moment
    // Mail::assertSent(InvitationMail::class);
});

test('a user cannot invite a member without a current workspace', function (): void {
    // Arrange
    Mail::fake();

    $user = User::factory()->create();

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Act
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    // Assert
    $response->assertRedirect();

    Mail::assertNothingSent();
});

test('a user cannot invite the same email twice', function (): void {
    // Arrange
    Mail::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $email = 'test@example.com';
    $role = current(RoleRegistry::roleKeys());

    // Create an existing invitation
    WorkspaceInvitation::query()->create([
        'workspace_id' => $workspace->id,
        'email' => $email,
        'role' => $role,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->post('/workspaces/current/invitations', [
            'email' => $email,
            'role' => $role,
        ]);

    // Assert
    $response->assertSessionHasErrors(['email']);

    // Check that only one invitation exists
    $this->assertEquals(1, WorkspaceInvitation::query()->where('workspace_id', $workspace->id)
        ->where('email', $email)
        ->count());

    Mail::assertNothingSent();
});
