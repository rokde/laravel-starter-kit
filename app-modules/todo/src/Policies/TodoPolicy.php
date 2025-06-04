<?php

declare(strict_types=1);

namespace Modules\Todo\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Todo\Models\Todo;
use Modules\Workspace\Models\Workspace;

class TodoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create todos for a specific user.
     */
    public function create(User $user, Workspace $workspace, int $forUserId): bool
    {
        // Check if the user belongs to the workspace
        if (! $user->belongsToWorkspace($workspace)) {
            return false;
        }

        // If the user is creating a todo for themselves, allow it
        if ($user->id === $forUserId) {
            return true;
        }

        // If the user is the owner of the workspace or has admin role, allow creating todos for other users
        return $user->ownsWorkspace($workspace) || $user->hasWorkspaceRole($workspace, 'admin') || $user->hasWorkspaceRole($workspace, 'editor');
    }

    /**
     * Determine whether the user can view the todo.
     */
    public function view(User $user, Todo $todo): bool
    {
        $workspace = $todo->workspace;

        // Check if the user belongs to the workspace
        return $user->belongsToWorkspace($workspace);
    }

    /**
     * Determine whether the user can update the todo.
     */
    public function update(User $user, Todo $todo): bool
    {
        $workspace = $todo->workspace;

        // Check if the user belongs to the workspace
        if (! $user->belongsToWorkspace($workspace)) {
            return false;
        }

        // If the user is the owner of the workspace or has admin role, allow updating any todo
        if ($user->ownsWorkspace($workspace) || $user->hasWorkspaceRole($workspace, 'admin')) {
            return true;
        }

        // Otherwise, only allow updating todos assigned to the user
        return $todo->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the todo.
     */
    public function delete(User $user, Todo $todo): bool
    {
        $workspace = $todo->workspace;

        // Check if the user belongs to the workspace
        if (! $user->belongsToWorkspace($workspace)) {
            return false;
        }

        // If the user is the owner of the workspace or has admin role, allow deleting any todo
        if ($user->ownsWorkspace($workspace) || $user->hasWorkspaceRole($workspace, 'admin')) {
            return true;
        }

        // Otherwise, only allow deleting todos assigned to the user
        return $todo->user_id === $user->id;
    }
}
