<?php

declare(strict_types=1);

namespace Modules\Workspace\Listeners;

use App\ValueObjects\Id;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Modules\Workspace\Actions\SetCurrentWorkspace;
use Modules\Workspace\Models\Workspace;

class SetWorkspaceForLoggedInUser
{
    public function __construct(
        private readonly SetCurrentWorkspace $setCurrentWorkspace,
    ) {}

    public function handle(Login|Authenticated $event): void
    {
        if ($event->user->workspace_id !== null) {
            return;
        }

        $userId = new Id($event->user->getAuthIdentifier());

        $workspace = Workspace::query()
            ->where('user_id', $userId)
            ->oldest('id')
            ->firstOrFail();

        $this->setCurrentWorkspace->handle(
            userId: $userId,
            workspaceId: new Id($workspace->id),
        );
    }
}
