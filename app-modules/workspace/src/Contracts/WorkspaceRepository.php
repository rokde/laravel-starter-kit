<?php

declare(strict_types=1);

namespace Modules\Workspace\Contracts;

use Illuminate\Support\Collection;
use Modules\Workspace\DataTransferObjects\Workspace;

interface WorkspaceRepository
{
    /**
     * @return Collection<Workspace>|Workspace[]
     */
    public function all(): Collection;
}
