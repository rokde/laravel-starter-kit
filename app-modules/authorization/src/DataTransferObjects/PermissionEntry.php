<?php

declare(strict_types=1);

namespace Modules\Authorization\DataTransferObjects;

use BackedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;
use ReturnTypeWillChange;
use Stringable;

readonly class PermissionEntry implements Arrayable, JsonSerializable, Stringable
{
    public string $resource;

    public string $action;

    public string $description;

    public function __construct(
        string|BackedEnum $resource,
        string|BackedEnum $action,
        string $description,
    )
    {
        $resource = $resource instanceof BackedEnum ? $resource->value : $resource;
        $this->resource = Str::slug(Str::singular($resource));
        $action = $action instanceof BackedEnum ? $action->value : $action;
        $this->action = Str::slug($action);
        $this->description = Str::trim($description);
    }

    public function __toString(): string
    {
        return $this->permission();
    }

    public function permission(): string
    {
        return $this->resource . '.' . $this->action;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->permission(),
            'resource' => $this->resource,
            'action' => $this->action,
            'description' => $this->description,
        ];
    }
}
