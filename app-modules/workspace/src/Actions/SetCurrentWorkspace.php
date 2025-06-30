<?php

declare(strict_types=1);

namespace Modules\Workspace\Actions;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Workspace\Models\Workspace;

class SetCurrentWorkspace
{
    public function handle(Id $userId, Id $workspaceId): void
    {
        $user = User::query()->findOrFail($userId->value());
        $workspace = Workspace::query()->findOrFail($workspaceId->value());

        Gate::authorize('make-current', $workspace);

        DB::transaction(function () use ($user, $workspace): void {
            $user->update(['workspace_id' => $workspace->id]);
        });

        setPermissionsTeamId($workspace->id);
    }
}
