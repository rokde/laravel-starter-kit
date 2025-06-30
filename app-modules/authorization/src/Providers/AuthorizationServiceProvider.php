<?php

declare(strict_types=1);

namespace Modules\Authorization\Providers;

use Illuminate\Support\ServiceProvider;
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
            ->registerPermission('workspace', 'view', 'View the workspace.')
            ->registerPermission('workspace', 'edit', 'Edit the workspace.');

        app(PermissionRegistry::class)
            ->removePermission('Workspace', 'view')
            ->removePermission('Workspace', 'edit');
    }
}
