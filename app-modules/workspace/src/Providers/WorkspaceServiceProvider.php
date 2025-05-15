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
        $this->app->bind(WorkspaceRepositoryContract::class, function (Application $app) {
            return new WorkspaceRepository($app['auth']->user());
        });
    }

    public function boot(): void
    {
        RoleRegistry::role('admin', 'Admin', 'The admin of the workspace.');
        RoleRegistry::role('editor', 'Editor', 'The normal co-working user of the workspace.');
        RoleRegistry::role('visitor', 'Visitor', 'A readonly user of the workspace.');
    }
}
