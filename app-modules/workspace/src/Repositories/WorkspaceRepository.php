<?php

declare(strict_types=1);

namespace Modules\Workspace\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\Workspace\Contracts\WorkspaceRepository as WorkspaceRepositoryContract;
use Modules\Workspace\DataTransferObjects\Owner;
use Modules\Workspace\DataTransferObjects\Workspace;
use Modules\Workspace\Models\Workspace as WorkspaceModel;

class WorkspaceRepository implements WorkspaceRepositoryContract
{
    public function __construct(private readonly ?User $user) {}

    public function all(): Collection
    {
        if ($this->user === null) {
            return collect();
        }

        return $this->user
            ->allWorkspaces()
            ->map(function (WorkspaceModel $workspace): Workspace {
                return new Workspace(
                    id: $workspace->id,
                    name: $workspace->name,
                    owner: new Owner(
                        id: $workspace->owner->id,
                        name: $workspace->owner->name,
                        email: $workspace->owner->email,
                    ),
                    currentWorkspace: $this->user->isCurrentWorkspace($workspace),
                );
            });
    }
}
