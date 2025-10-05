<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\ValueObjects\Id;
use Modules\Workspace\Models\WorkspaceInvitation;

class RevokeTeamInvitation
{
    public function handle(Id|WorkspaceInvitation $invitationId): void
    {
        /** @var WorkspaceInvitation $workspaceInvitation */
        $workspaceInvitation = $invitationId;
        if ($invitationId instanceof Id) {
            $workspaceInvitation = WorkspaceInvitation::query()->findOrFail($invitationId->value());
        }

        // delete invitation
        $workspaceInvitation->delete();
    }
}
