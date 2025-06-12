<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class NewUser
{
    public string $email;

    public function __construct(
        public string $name,
        string $email,
        public string $locale,
        public string $timezone,
    ) {
        $this->email = mb_strtolower(mb_trim($email));
    }
}
