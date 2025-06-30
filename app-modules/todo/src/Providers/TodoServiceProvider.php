<?php

declare(strict_types=1);

namespace Modules\Todo\Providers;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\ServiceProvider;
use Modules\Authorization\Enums\PermissionActions;
use Modules\Authorization\Permissions\PermissionRegistry;
use Modules\Todo\Jobs\NotifyTodaysDueTaskJob;

class TodoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register bindings
    }

    public function boot(): void
    {
        // Bootstrap the module
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        app(PermissionRegistry::class)
            ->registerPermission('todo', PermissionActions::VIEW, 'View todos.')
            ->registerPermission('todo', PermissionActions::EDIT, 'Create and update todos.')
            ->registerPermission('todo', PermissionActions::DELETE, 'Delete todos.');

        Schedule::job(new NotifyTodaysDueTaskJob)->dailyAt('00:01');
    }
}
