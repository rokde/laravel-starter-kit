<?php

declare(strict_types=1);

use Modules\Workspace\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    expect($workspace->users)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
    expect($workspace->invitations)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

test('workspace can be retrieved by id', function (): void {
    // Arrange
    $workspace = Workspace::factory()->create();

    // Act
    $found = Workspace::find($workspace->id);

    // Assert
    expect($found)->not()->toBeNull();
    expect($found->id)->toBe($workspace->id);
    expect($found->name)->toBe($workspace->name);
});
