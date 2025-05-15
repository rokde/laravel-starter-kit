<?php

declare(strict_types=1);

namespace Modules\Workspace\Contracts;

use Illuminate\Support\Collection;

interface WorkspaceRepository
{
    /**
     * @return Collection<\Modules\Workspace\DataTransferObjects\Workspace>|\Modules\Workspace\DataTransferObjects\Workspace[]
     */
    public function all(): Collection;
}
