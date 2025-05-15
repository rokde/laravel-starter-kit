<?php

declare(strict_types=1);

namespace Modules\Workspace\DataTransferObjects;

class Owner
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
    ) {}
}
