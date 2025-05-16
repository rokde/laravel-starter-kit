<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Workspace\Events\MemberAttached;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

class AcceptTeamInvitation
{
    public function handle(Id|WorkspaceInvitation $invitationId, Id|User $userId): Workspace
    {
        /** @var WorkspaceInvitation $workspaceInvitation */
        $workspaceInvitation = $invitationId;
        if ($invitationId instanceof Id) {
            $workspaceInvitation = WorkspaceInvitation::findOrFail($invitationId->value());
        }

        /** @var User $user */
        $user = $userId;
        if ($userId instanceof Id) {
            $user = User::findOrFail($userId->value());
        }

        return DB::transaction(function () use ($workspaceInvitation, $user): Workspace {
            $workspace = $workspaceInvitation->workspace;

            if ($user->getAuthIdentifier() === $workspace->owner->getAuthIdentifier()) {
                throw new Exception('The owner can not be added with another role too.');
            }

            // attach user
            $workspace->users()->attach($user, [
                'role' => $workspaceInvitation->role,
            ]);

            // delete invitation
            $workspaceInvitation->delete();

            // switch user to workspace
            $user->switchWorkspace($workspace);

            event(new MemberAttached($workspace, $user));

            return $workspace;
        });
    }
}
