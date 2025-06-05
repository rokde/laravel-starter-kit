<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Modules\Workspace\Mail\InvitationMail;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

class InviteMember
{
    public function handle(Id|Workspace $workspaceId, string $email, string $role): WorkspaceInvitation
    {
        $workspace = $workspaceId;
        if ($workspaceId instanceof Id) {
            $workspace = Workspace::query()->findOrFail($workspaceId->value());
        }

        Gate::authorize('addMember', $workspace);

        $invitation = DB::transaction(fn (): WorkspaceInvitation => $workspace->invitations()->create([
            'email' => $email,
            'role' => $role,
        ]));

        Mail::to($email)
            ->send(new InvitationMail($invitation));

        return $invitation;
    }
}
