<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Modules\Workspace\Models\Membership;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Notifications\WelcomeNewWorkspaceOwnerNotification;
use Modules\Workspace\Notifications\WorkspaceTransferredNotification;

class TransferWorkspaceOwnership
{
    /**
     * @throws AuthorizationException when the user is not able to transform the ownership
     */
    public function handle(
        Id|Workspace $workspaceId,
        Id|User $currentOwnerId,
        Id|User $newOwnerId,
        string $role = 'admin',
    ): void {
        /** @var Workspace $workspace */
        $workspace = $workspaceId;
        if ($workspaceId instanceof Id) {
            $workspace = User::findOrFail($workspaceId->value());
        }

        /** @var User $currentOwner */
        $currentOwner = $currentOwnerId;
        if ($currentOwnerId instanceof Id) {
            $currentOwner = User::findOrFail($currentOwnerId->value());
        }

        /** @var User $newOwner */
        $newOwner = $newOwnerId;
        if ($newOwnerId instanceof Id) {
            $newOwner = User::findOrFail($newOwnerId->value());
        }

        throw_unless($currentOwner->can('transferOwnership', [$workspace, $newOwner]), AuthorizationException::class);

        DB::transaction(function () use ($workspace, $currentOwner, $newOwner, $role): void {
            Membership::query()
                ->where('user_id', $newOwner->id)
                ->update([
                    'user_id' => $currentOwner->id,
                    'role' => $role,
                ]);

            $workspace->update(['user_id' => $newOwner->id]);
        });

        $currentOwner->notify(new WorkspaceTransferredNotification($workspace->toDto()));
        $newOwner->notify(new WelcomeNewWorkspaceOwnerNotification($workspace->toDto()));
    }
}
