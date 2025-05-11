<?php

declare(strict_types=1);

namespace Modules\Workspace\Listeners;

use App\ValueObjects\Id;
use Exception;
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

        try {
            $workspace = Workspace::query()
                ->where('user_id', $userId)
                ->oldest('id')
                ->firstOrFail();
        } catch (Exception) {
            return;
        }

        $this->setCurrentWorkspace->handle(
            userId: $userId,
            workspaceId: new Id($workspace->id),
        );
    }
}
