<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Workspace\Events\MemberUpdated;
use Modules\Workspace\Models\Workspace;

class UpdateMember
{
    public function handle(Id|Workspace $workspaceId, Id $memberUserId, string $role): Workspace
    {
        $workspace = $workspaceId;
        if ($workspaceId instanceof Id) {
            $workspace = Workspace::query()->findOrFail($workspaceId->value());
        }

        Gate::authorize('updateMember', $workspace);

        DB::transaction(function () use ($workspace, $memberUserId, $role): void {
            $workspace->users()->updateExistingPivot($memberUserId->value(), [
                'role' => $role,
            ]);
        });

        $member = User::query()->find($memberUserId->value());
        MemberUpdated::dispatch($workspace, $member);

        return $workspace;
    }
}
