<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

use JsonSerializable;
use ReturnTypeWillChange;

class Role implements JsonSerializable
{
    /**
     * Create a new role instance.
     */
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $description = '',
    ) {}

    /**
     * Get the JSON serializable representation of the object.
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'name' => __($this->name),
            'description' => __($this->description),
        ];
    }
}
