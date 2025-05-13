<?php

declare(strict_types=1);

namespace Modules\Workspace\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Workspace\Models\RoleRegistry;

class WorkspaceServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {

        RoleRegistry::role('admin', 'Admin', 'The admin of the workspace.');
        RoleRegistry::role('editor', 'Editor', 'The normal co-working user of the workspace.');
        RoleRegistry::role('visitor', 'Visitor', 'A readonly user of the workspace.');
    }
}
