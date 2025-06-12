<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class User extends NewUser
{
    public function __construct(
        public int $id,
        string $name,
        string $email,
        public bool $verified,
        string $locale,
        string $timezone,
    ) {
        parent::__construct($name, $email, $locale, $timezone);
    }
}
