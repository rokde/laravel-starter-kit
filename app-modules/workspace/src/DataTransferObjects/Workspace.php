<?php

declare(strict_types=1);

namespace Modules\Workspace\DataTransferObjects;

final readonly class Workspace
{
    public function __construct(
        public int $id,
        public string $name,
        public Owner $owner,
        public bool $currentWorkspace,
    ) {}
}
