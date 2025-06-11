<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public bool $verified,
        public string $locale,
        public string $timezone,
    ) {}
}
