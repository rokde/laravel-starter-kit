<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Todo\Models\Todo;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('it can be instantiated using factory', function (): void {
    $todo = Todo::factory()->create();

    expect($todo)->toBeInstanceOf(Todo::class)
        ->and($todo->id)->toBeInt()
        ->and($todo->title)->toBeString()
        ->and($todo->completed)->toBeBool();
});

test('it belongs to a workspace', function (): void {
    $workspace = Workspace::factory()->create();
    $todo = Todo::factory()->create(['workspace_id' => $workspace->id]);

    expect($todo->workspace)->toBeInstanceOf(Workspace::class)
        ->and($todo->workspace->id)->toBe($workspace->id);
});

test('it belongs to a user', function (): void {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id]);

    expect($todo->user)->toBeInstanceOf(User::class)
        ->and($todo->user->id)->toBe($user->id);
});

test('it can be marked as completed', function (): void {
    $todo = Todo::factory()->create(['completed' => false]);

    $todo->completed = true;
    $todo->save();

    expect($todo->completed)->toBeTrue();
});

test('it can be marked as incomplete', function (): void {
    $todo = Todo::factory()->create(['completed' => true]);

    $todo->completed = false;
    $todo->save();

    expect($todo->completed)->toBeFalse();
});
