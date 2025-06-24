<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Actions\TransferWorkspaceOwnership;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $otherUser = User::factory()->create();
    $otherWorkspace = Workspace::factory()->create(['user_id' => $otherUser->id]);
    $otherUser->switchWorkspace($workspace);

    $workspace->users()->attach($otherUser, [
        'role' => 'admin',
    ]);

    $this->owner = $user->fresh();
    $this->workspace = $workspace->fresh();
    $this->memberUser = $otherUser->fresh();
    $this->foreignWorkspace = $otherWorkspace;

    expect($this->memberUser->belongsToWorkspace($this->workspace))->toBeTrue();
});

test('workspace owner can be transferred to any member', function (): void {
    $transferOwnership = new TransferWorkspaceOwnership();
    $transferOwnership->handle($this->workspace, $this->owner, $this->memberUser);

    expect($this->workspace->fresh()->user_id)->toBe($this->memberUser->id);
    expect($this->workspace->users->contains($this->owner))->toBeTrue();
});

test('workspace can not be transferred by a member', function (): void {
    $this->expectException(AuthorizationException::class);
    $transferOwnership = new TransferWorkspaceOwnership();
    $transferOwnership->handle($this->workspace, $this->memberUser, $this->owner);
});

test('workspace can not be transferred to itself', function (): void {
    $this->expectException(AuthorizationException::class);
    $transferOwnership = new TransferWorkspaceOwnership();
    $transferOwnership->handle($this->workspace, $this->owner, $this->owner);
});
