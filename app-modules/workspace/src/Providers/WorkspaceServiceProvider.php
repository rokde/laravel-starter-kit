<?php

declare(strict_types=1);

namespace Modules\Workspace\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Modules\Workspace\Contracts\WorkspaceRepository as WorkspaceRepositoryContract;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Repositories\WorkspaceRepository;

class WorkspaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WorkspaceRepositoryContract::class, fn (Application $app): WorkspaceRepository => new WorkspaceRepository($app->make(\Illuminate\Contracts\Auth\Factory::class)->user()));
    }

    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');
        $this->loadTranslationsFrom(__DIR__.'/../../lang');

        RoleRegistry::role('admin', 'Admin', 'The admin of the workspace.');
        RoleRegistry::role('editor', 'Editor', 'The normal co-working user of the workspace.');
        RoleRegistry::role('visitor', 'Visitor', 'A readonly user of the workspace.');
    }
}
