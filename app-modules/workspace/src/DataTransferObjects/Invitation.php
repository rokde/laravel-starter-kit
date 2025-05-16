<?php

declare(strict_types=1);

namespace Modules\Workspace\DataTransferObjects;

final readonly class Invitation
{
    public function __construct(
        public int $id,
        public string $email,
        public string $role,
        public string $created_at,
        public ?string $link = null,
    ) {}
}
