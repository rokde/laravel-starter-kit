<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use ReturnTypeWillChange;
use Stringable;

readonly class Id implements JsonSerializable, Stringable
{
    private int $id;

    public function __construct(int|string|null $id)
    {
        throw_if(is_null($id), new InvalidArgumentException('Id cannot be null'));

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

    /**
     * @param  array{id: int}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = (int) ($data['id']);
    }

    public function value(): int
    {
        return $this->id;
    }

    /**
     * @return array{id: int}
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
