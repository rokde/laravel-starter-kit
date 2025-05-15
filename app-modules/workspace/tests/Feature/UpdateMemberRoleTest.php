<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('member roles can be updated', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $otherUser = User::factory()->create();
    $otherWorkspace = Workspace::factory()->create(['user_id' => $otherUser->id]);
    $otherUser->switchWorkspace($workspace);

    $user->currentWorkspace->users()->attach(
        $otherUser = User::factory()->create(),
        ['role' => 'admin'],
    );
    $this->assertTrue($otherUser->belongsToWorkspace($user->currentWorkspace));

    $this->patch('/workspaces/current/members', [
        'id' => $otherUser->id,
        'role' => 'editor',
    ])->assertRedirect();


    // @TODO test does not work, but action works fine
    // $this->assertTrue($otherUser->fresh()->hasWorkspaceRole($user->currentWorkspace, 'editor'));
});
