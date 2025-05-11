<?php

namespace Modules\Workspace\Actions;

use App\ValueObjects\Id;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Workspace\Models\Workspace;

class UpdateWorkspaceName
{
    public function handle(Id|Workspace $workspaceId, string $name): void
    {
        $workspace = $workspaceId;
        if ($workspaceId instanceof Id) {
            $workspace = Workspace::query()->findOrFail($workspaceId->value());
        }

        Gate::authorize('update', $workspace);

        DB::transaction(function () use ($workspace, $name): void {
            $workspace->update(['name' => $name]);
        });
    }
}
