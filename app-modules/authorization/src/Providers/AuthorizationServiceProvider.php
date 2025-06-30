<?php

declare(strict_types=1);

namespace Modules\Authorization\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Authorization\Enums\PermissionActions;
use Modules\Authorization\Enums\PermissionResources;
use Modules\Authorization\Permissions\PermissionRegistry;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PermissionRegistry::class, fn() => new PermissionRegistry());
    }

    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');

        app(PermissionRegistry::class)
            ->registerPermission(PermissionResources::ROLE, PermissionActions::VIEW, 'View the roles.')
            ->registerPermission(PermissionResources::ROLE, PermissionActions::EDIT, 'Add or update roles.')
            ->registerPermission(PermissionResources::ROLE, PermissionActions::DELETE, 'Delete roles.');
    }
}
