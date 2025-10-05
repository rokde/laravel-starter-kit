<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('workspace has expected attributes', function (): void {
    // Arrange
    $name = 'Test Workspace';

    // Act
    $workspace = Workspace::factory()->create([
        'name' => $name,
    ]);

    // Assert
    expect($workspace->name)->toBe($name);
    expect($workspace->owner)->not()->toBeNull();
    expect($workspace->users)->toBeInstanceOf(Collection::class);
    expect($workspace->invitations)->toBeInstanceOf(Collection::class);
});

test('workspace can be retrieved by id', function (): void {
    // Arrange
    $workspace = Workspace::factory()->create();

    // Act
    $found = Workspace::query()->find($workspace->id);

    // Assert
    expect($found)->not()->toBeNull();
    expect($found->id)->toBe($workspace->id);
    expect($found->name)->toBe($workspace->name);
});
