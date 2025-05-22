<?php

declare(strict_types=1);

arch('Test the domain module boundaries for the workspace module.')
    ->expect('Modules\Workspace')
    ->toOnlyBeUsedIn('Modules\Workspace')
    ->ignoring([
        'Modules\Workspace\Contracts',
        'Modules\Workspace\DataTransferObjects',
        'Modules\Workspace\Events',
        'Modules\Workspace\Models\Concerns\ManagesWorkspaces',
        'Modules\Workspace\Models\Workspace',
    ]);

arch('Test the domain module boundaries for the workspace module (Workspace model).')
    ->expect('Modules\Workspace\Models\Workspace')
    ->toOnlyBeUsedIn([
        'Modules\Workspace',
        'Modules\Todo\Database\Factories\TodoFactory',
        'Modules\Todo\Models',
        'Modules\Todo\Policies',
    ]);
