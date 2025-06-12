<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Modules\Workspace\Mail\InvitationMail;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;
use Modules\Workspace\Notifications\GotWorkspaceInvitationNotification;

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

        try {
            $user = User::query()
                ->where('email', $email)
                ->firstOrFail();

            $user->notify(new GotWorkspaceInvitationNotification($invitation, $workspace->name));
        } catch (ModelNotFoundException) {
            Mail::to($email)
                ->send(new InvitationMail($invitation));
        }

        return $invitation;
    }
}
