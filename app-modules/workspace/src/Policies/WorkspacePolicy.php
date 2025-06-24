<?php

declare(strict_types=1);

namespace Modules\Workspace\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Workspace\Models\Workspace;
use Modules\Workspace\Models\WorkspaceInvitation;

class WorkspacePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        return $user->belongsToWorkspace($workspace);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        return $user->ownsWorkspace($workspace);
    }

    /**
     * Determin whether the user can make the workspace as its current workspace.
     */
    public function makeCurrent(User $user, Workspace $workspace): bool
    {
        return $user->belongsToWorkspace($workspace);
    }

    /**
     * Determine whether the user can add workspace members.
     */
    public function addMember(User $user, Workspace $workspace): bool
    {
        if ($user->ownsWorkspace($workspace)) {
            return true;
        }

        return $user->hasWorkspaceRole($workspace, 'admin');
    }

    /**
     * Determine whether the user can update workspace member.
     */
    public function updateMember(User $user, Workspace $workspace): bool
    {
        if ($user->ownsWorkspace($workspace)) {
            return true;
        }

        return $user->hasWorkspaceRole($workspace, 'admin');
    }

    /**
     * Determine whether the user can remove workspace members.
     */
    public function removeMember(User $user, Workspace $workspace, ?User $member = null): bool
    {
        // do not remove the owner
        if ($member && $member->id === $workspace->user_id) {
            return false;
        }

        // member has to be on the workspace
        if ($member && ! $member->belongsToWorkspace($workspace)) {
            return false;
        }
        if ($user->ownsWorkspace($workspace)) {
            return true;
        }

        return $user->hasWorkspaceRole($workspace, 'admin');
    }

    public function revokeInvitation(User $user, Workspace $workspace, ?WorkspaceInvitation $invitation = null): bool
    {
        if ($invitation && $invitation->email === $workspace->owner->email) {
            return false;
        }
        if ($user->ownsWorkspace($workspace)) {
            return true;
        }

        return $user->hasWorkspaceRole($workspace, 'admin');
    }

    /**
     * Determine whether the user can transfer the ownership to another member
     */
    public function transferOwnership(User $user, Workspace $workspace, ?User $newOwner = null): bool
    {
        if (!$user->ownsWorkspace($workspace)) {
            return false;
        }

        if ($newOwner === null) {
            return true;
        }

        if ($newOwner->is($user)) {
            return false;
        }

        return $newOwner->belongsToWorkspace($workspace);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $user->ownsWorkspace($workspace);
    }
}
