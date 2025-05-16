<?php

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Workspace\Events\MemberRemoved;
use Modules\Workspace\Models\Workspace;

class RemoveMember
{
    public function handle(Id|Workspace $workspaceId, Id|User $memberId): void
    {
        /** @var Workspace $workspace */
        $workspace = $workspaceId;
        if ($workspaceId instanceof Id) {
            $workspace = Workspace::query()->findOrFail($workspaceId->value());
        }

        /** @var User $member */
        $member = $memberId;
        if ($memberId instanceof Id) {
            $member = User::query()->findOrFail($memberId->value());
        }

        Gate::authorize('removeMember', [$workspace, $member]);

        DB::transaction(function () use ($workspace, $member): void {
            if ($member->isCurrentWorkspace($workspace)) {
                $member->forceFill([
                    'workspace_id' => null,
                ])->save();
            }

            $workspace->users()->detach($member);
        });

        MemberRemoved::dispatch($workspace, $member);
    }
}
