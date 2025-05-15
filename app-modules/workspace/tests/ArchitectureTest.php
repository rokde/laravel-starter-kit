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
    ]);
