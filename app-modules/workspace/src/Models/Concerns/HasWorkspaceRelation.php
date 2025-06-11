<?php

declare(strict_types=1);

namespace Modules\Workspace\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Workspace\Models\Workspace;

/**
 * @property-read \Illuminate\Database\Eloquent\Model $this
 */
trait HasWorkspaceRelation
{
    /**
     * Scope for a given workspace
     */
    #[Scope]
    protected function forWorkspace(Builder $query, Workspace|int $workspaceOrId): void
    {
        $query->where('workspace_id', $workspaceOrId instanceof Workspace ? $workspaceOrId->id : $workspaceOrId);
    }

    /**
     * Scope for the current workspace of the current user
     */
    #[Scope]
    protected function currentWorkspace(Builder $query): void
    {
        $workspace = request()->user()->currentWorkspace;
        if (!$workspace) {
            throw new \RuntimeException('Current user has no workspace assigned');
        }

        $query->where('workspace_id', $workspace->id);
    }
}
