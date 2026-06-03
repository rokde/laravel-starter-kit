<?php

declare(strict_types=1);

use Modules\Todo\Database\Factories\TodoFactory;
use Modules\Workspace\Models\Concerns\HasWorkspaceRelation;
use Modules\Workspace\Models\Concerns\ManagesWorkspaces;
use Modules\Workspace\Models\Workspace;

arch('Test the domain module boundaries for the workspace module.')
    ->expect('Modules\Workspace')
    ->toOnlyBeUsedIn('Modules\Workspace')
    ->ignoring([
        'Modules\Workspace\Contracts',
        'Modules\Workspace\DataTransferObjects',
        'Modules\Workspace\Events',
        ManagesWorkspaces::class,
        HasWorkspaceRelation::class,
        Workspace::class,
        'Modules\Workspace\Notifications',
    ]);

arch('Test the domain module boundaries for the workspace module (Workspace model).')
    ->expect(Workspace::class)
    ->toOnlyBeUsedIn([
        'Modules\Workspace',
        TodoFactory::class,
        'Modules\Todo\Models',
        'Modules\Todo\Policies',
        'Modules\Todo\Http\Controllers',
    ]);
