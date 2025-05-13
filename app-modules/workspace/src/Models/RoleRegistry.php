<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

final class RoleRegistry
{
    /**
     * The roles that are available to assign to users.
     *
     * @var array<string, Role>
     */
    public static $roles = [];

    /**
     * Determine if there are registered roles.
     */
    public static function hasRoles(): bool
    {
        return count(self::$roles) > 0;
    }

    /**
     * Find the role with the given key.
     */
    public static function findRole(string $key): ?Role
    {
        return self::$roles[$key] ?? null;
    }

    /**
     * Define a role.
     */
    public static function role(string $key, string $name, string $description = ''): Role
    {
        return tap(new Role($key, $name, $description), function ($role) use ($key): void {
            static::$roles[$key] = $role;
        });
    }

    /**
     * @return array<array-key, string>
     */
    public static function roleKeys(): array
    {
        return array_keys(self::$roles);
    }
}
