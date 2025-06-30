<?php

declare(strict_types=1);

arch('Test the domain module boundaries for the workspace module.')
    ->expect('Modules\Workspace')
    ->toOnlyBeUsedIn('Modules\Workspace')
    ->ignoring([
        'Modules\Workspace\Contracts',
        'Modules\Workspace\DataTransferObjects',
        'Modules\Workspace\Events',
        Modules\Workspace\Models\Concerns\ManagesWorkspaces::class,
        Modules\Workspace\Models\Concerns\HasWorkspaceRelation::class,
        Modules\Workspace\Models\Workspace::class,
        'Modules\Workspace\Notifications',
    ]);

arch('Test the domain module boundaries for the workspace module (Workspace model).')
    ->expect(Modules\Workspace\Models\Workspace::class)
    ->toOnlyBeUsedIn([
        'Modules\Workspace',
        Modules\Todo\Database\Factories\TodoFactory::class,
        'Modules\Todo\Models',
        'Modules\Todo\Policies',
        'Modules\Todo\Http\Controllers',
    ]);
