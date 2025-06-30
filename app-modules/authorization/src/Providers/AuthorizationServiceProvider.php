<?php

declare(strict_types=1);

namespace Modules\Authorization\Providers;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\ServiceProvider;
use Modules\Authorization\Enums\PermissionActions;
use Modules\Authorization\Enums\PermissionResources;
use Modules\Authorization\Http\Middleware\SetWorkspaceForPermissions;
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

        /** @var Kernel $kernel */
        $kernel = app(Kernel::class);

        $kernel->addToMiddlewarePriorityBefore(
            SubstituteBindings::class,
            SetWorkspaceForPermissions::class,
        );

        $kernel->appendMiddlewareToGroup('web', SetWorkspaceForPermissions::class);
    }
}
