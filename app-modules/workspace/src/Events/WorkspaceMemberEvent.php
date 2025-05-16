<?php

declare(strict_types=1);

namespace Modules\Workspace\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Workspace\Models\Workspace;

abstract class WorkspaceMemberEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Workspace $workspace, public readonly User $user) {}
}
