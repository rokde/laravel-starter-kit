<?php

declare(strict_types=1);

namespace Modules\Workspace\DataTransferObjects;

final readonly class Owner
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
