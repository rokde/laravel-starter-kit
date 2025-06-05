<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use ReturnTypeWillChange;
use Serializable;
use Stringable;

class Id implements Stringable, JsonSerializable, Serializable
{
    private readonly int $id;

    public function __construct(int|string|null $id)
    {
        if (is_null($id)) {
            throw new InvalidArgumentException('Id cannot be null');
        }

        $this->id = (int) $id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function __serialize(): array
    {
        return $this->jsonSerialize();
    }

    public function __unserialize(array $data): void
    {
        $this->id = (int) $data['id'];
    }

    public function value(): int
    {
        return $this->id;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function serialize(): string
    {
        return $this->__toString();
    }

    public function unserialize(string $data): void
    {
        $this->id = (int) $data;
    }
}
