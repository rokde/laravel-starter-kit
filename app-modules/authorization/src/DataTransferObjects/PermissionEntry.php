<?php

declare(strict_types=1);

namespace Modules\Authorization\DataTransferObjects;

use Illuminate\Support\Str;

readonly class PermissionEntry
{
    public string $resource;

    public string $action;

    public string $description;

    public function __construct(
        string $resource,
        string $action,
        string $description,
    )
    {
        $this->resource = Str::slug($resource);
        $this->action = Str::slug($action);
        $this->description = Str::trim($description);
    }

    public function permission(): string
    {
        return $this->resource . '.' . $this->action;
    }
}
