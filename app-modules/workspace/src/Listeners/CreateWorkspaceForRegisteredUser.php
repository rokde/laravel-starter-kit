<?php

declare(strict_types=1);

namespace Modules\Workspace\Listeners;

use App\ValueObjects\Id;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Workspace\Actions\CreateWorkspace;

class CreateWorkspaceForRegisteredUser implements ShouldQueue
{
    public function __construct(
        private readonly CreateWorkspace $createWorkspace,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $ownerId = new Id($event->user->getAuthIdentifier());

        $this->createWorkspace->handle(
            ownerId: $ownerId,
            name: 'My workspace',
        );
    }
}
