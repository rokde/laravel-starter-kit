<?php

declare(strict_types=1);

namespace Modules\Passkey\DataTransferObjects;

final readonly class Passkey
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $last_used_at = null,
    ) {}
}
