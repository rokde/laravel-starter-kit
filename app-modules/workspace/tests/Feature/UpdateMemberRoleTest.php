<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Workspace\Events\MemberUpdated;
use Modules\Workspace\Models\Workspace;

uses(RefreshDatabase::class);

test('member roles can be updated', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['user_id' => $user->id]);
    $user->switchWorkspace($workspace);

    $otherUser = User::factory()->create();
    Workspace::factory()->create(['user_id' => $otherUser->id]);
    $otherUser->switchWorkspace($workspace);

    $eventWasCalled = false;
    \Illuminate\Support\Facades\Event::listen(MemberUpdated::class, function (MemberUpdated $event) use ($otherUser, &$eventWasCalled): void {
        $this->assertTrue($event->user->id === $otherUser->id);
        $eventWasCalled = true;
    });

    $user->currentWorkspace->users()->attach(
        $otherUser,
        ['role' => 'admin'],
    );
    $this->assertTrue($otherUser->fresh()->belongsToWorkspace($workspace));

    $this
        ->actingAs($user)
        ->withoutExceptionHandling()
        ->patch(route('workspaces.members.update'), [
            'id' => $otherUser->id,
            'role' => 'editor',
        ])->assertRedirect();

    $this->assertTrue($otherUser->fresh()->hasWorkspaceRole($user->currentWorkspace, 'editor'));
    $this->assertTrue($eventWasCalled);
});
