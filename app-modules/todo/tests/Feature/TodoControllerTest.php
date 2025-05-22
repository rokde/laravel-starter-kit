<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todo\Models\Todo;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('user can view todos for current workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'title' => 'Test Todo',
    ]);

    $response = $this->actingAs($user)->get(route('todos.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('todo::Index')
        ->has('todos', 1)
        ->where('todos.0.id', $todo->id)
        ->where('todos.0.title', 'Test Todo')
    );
});

test('user can create todo', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $response = $this->actingAs($user)->get(route('todos.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('todo::Create')
        ->has('workspace')
        ->has('workspaceUsers')
    );
});

test('user can store todo', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $response = $this->actingAs($user)->post(route('todos.store'), [
        'title' => 'New Todo',
        'user_id' => $user->id,
    ]);

    $response->assertRedirect(route('todos.index'));
    expect(Todo::where([
        'title' => 'New Todo',
        'user_id' => $user->id,
        'workspace_id' => $workspace->id,
    ])->exists())->toBeTrue();
});

test('user can update todo', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'title' => 'Original Title',
    ]);

    $response = $this->actingAs($user)->patch(route('todos.update', $todo), [
        'title' => 'Updated Title',
    ]);

    $response->assertRedirect(route('todos.index'));
    expect(Todo::where([
        'id' => $todo->id,
        'title' => 'Updated Title',
    ])->exists())->toBeTrue();
});

test('user can toggle todo completion', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
        'completed' => false,
    ]);

    $response = $this->actingAs($user)->patch(route('todos.toggle-complete', $todo));

    $response->assertRedirect(route('todos.index'));
    expect(Todo::where([
        'id' => $todo->id,
        'completed' => true,
    ])->exists())->toBeTrue();
});

test('user can delete todo', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $todo = Todo::factory()->create([
        'workspace_id' => $workspace->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('todos.destroy', $todo));

    $response->assertRedirect(route('todos.index'));
    expect(Todo::where('id', $todo->id)->exists())->toBeFalse();
});
