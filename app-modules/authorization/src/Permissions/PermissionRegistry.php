<?php

declare(strict_types=1);

namespace Modules\Authorization\Permissions;

use Illuminate\Support\Collection;
use Modules\Authorization\DataTransferObjects\PermissionEntry;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionRegistry
{
    /**
     * @var array<string, PermissionEntry>
     */
    private array $permissions = [];

    private array $removePermissions = [];

    public function registerPermission(string $resource, string $action, string $description): self
    {
        $permissionEntry = new PermissionEntry($resource, $action, $description);

        return $this->register($permissionEntry);
    }

    public function register(PermissionEntry $permissionEntry): self
    {
        $this->permissions[$permissionEntry->permission()] = $permissionEntry;

        return $this;
    }

    /**
     * @return Collection<PermissionEntry>
     */
    public function permissions(): Collection
    {
        return collect($this->permissions)
            ->filter(fn(PermissionEntry $entry, string $permission) => !in_array($permission, $this->removePermissions))->values();
    }

    public function removePermission(string $resource, string $action): self
    {
        $permissionEntry = new PermissionEntry($resource, $action, '');

        return $this->remove($permissionEntry);
    }

    public function remove(PermissionEntry $permissionEntry): self
    {
        $this->removePermissions[] = $permissionEntry->permission();

        return $this;
    }

    public function commit(): self
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        collect($this->permissions)
            ->keys()
            ->each(fn(string $permission) => Permission::firstOrCreate(['name' => $permission]));

        collect($this->removePermissions)
            ->each(function (string $permission): void {
                try {
                    Permission::findByName($permission)->delete();
                } catch (PermissionDoesNotExist) {
                }
            });

        return $this;
    }
}
