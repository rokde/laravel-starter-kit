<?php

declare(strict_types=1);

namespace Modules\Authorization\DataTransferObjects;

use Spatie\Permission\Models\Role as RoleModel;

readonly class Role
{
    public function __construct(
        public int   $id,
        public string $name,
        public array $permissions,
    )
    {
    }

    public static function fromModel(RoleModel $role): static
    {
        $role->loadMissing('permissions');

        return new static(
            id: $role->id,
            name: $role->name,
            permissions: $role->permissions->pluck('name')->toArray(),
        );
    }
}
