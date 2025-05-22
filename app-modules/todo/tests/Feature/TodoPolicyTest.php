<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todo\Models\Todo;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('user can create todo for themselves', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    // Check if the user can create a todo for themselves
    expect($user->can('create', [Todo::class, $workspace, $user->id]))->toBeTrue();
});

test('user cannot create todo for another user without admin role', function (): void {
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $anotherUser->id]);

    // Add the user to the workspace (not as admin)
    $workspace->users()->attach($user, ['role' => 'editor']);
    $user->switchWorkspace($workspace);

    // Check if the user can create a todo for another user
    expect($user->can('create', [Todo::class, $workspace, $anotherUser->id]))->toBeFalse();
});

test('admin can create todo for another user', function (): void {
    $owner = User::factory()->create();
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();

    // Create a workspace owned by the owner
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Add the user to the workspace as admin
    $workspace->users()->attach($user, ['role' => 'admin']);
    $workspace->users()->attach($anotherUser, ['role' => 'editor']);

    $user->switchWorkspace($workspace);

    // Check if the admin user can create a todo for another user
    expect($user->can('create', [Todo::class, $workspace, $anotherUser->id]))->toBeTrue();
});

test('owner can create todo for another user', function (): void {
    $owner = User::factory()->create();
    $anotherUser = User::factory()->create();

    // Create a workspace owned by the owner
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Add another user to the workspace
    $workspace->users()->attach($anotherUser, ['role' => 'editor']);

    $owner->switchWorkspace($workspace);

    // Check if the owner can create a todo for another user
    expect($owner->can('create', [Todo::class, $workspace, $anotherUser->id]))->toBeTrue();
});

test('user can update their own todo', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
    ]);

    // Check if the user can update their own todo
    expect($user->can('update', $todo))->toBeTrue();
});

test('user cannot update another users todo without admin role', function (): void {
    $owner = User::factory()->create();
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();

    // Create a workspace owned by the owner
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Add users to the workspace
    $workspace->users()->attach($user, ['role' => 'editor']);
    $workspace->users()->attach($anotherUser, ['role' => 'editor']);

    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $anotherUser->id,
    ]);

    // Check if the user can update another user's todo
    expect($user->can('update', $todo))->toBeFalse();
});

test('admin can update another users todo', function (): void {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $anotherUser = User::factory()->create();

    // Create a workspace owned by the owner
    $workspace = Workspace::factory()->create(['user_id' => $owner->id]);

    // Add users to the workspace
    $workspace->users()->attach($admin, ['role' => 'admin']);
    $workspace->users()->attach($anotherUser, ['role' => 'editor']);

    $admin->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $anotherUser->id,
    ]);

    // Check if the admin can update another user's todo
    expect($admin->can('update', $todo))->toBeTrue();
});
