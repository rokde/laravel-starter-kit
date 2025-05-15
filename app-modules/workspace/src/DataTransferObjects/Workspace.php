<?php

declare(strict_types=1);

namespace Modules\Workspace\DataTransferObjects;

class Workspace
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly Owner $owner,
        public readonly bool $currentWorkspace,
    ) {}
}
