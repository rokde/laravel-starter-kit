<?php

namespace Modules\Notification\DataTransferObjects;

final readonly class Notification
{
    public function __construct(
        public string $id,
        public string $type,
        public string $title,
        public ?string $url,
        public array $data,
        public bool $read,
    ) {}
}
