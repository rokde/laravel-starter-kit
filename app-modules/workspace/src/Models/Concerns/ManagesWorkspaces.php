<?php

declare(strict_types=1);

namespace Modules\Workspace\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Workspace\Models\Membership;
use Modules\Workspace\Models\OwnerRole;
use Modules\Workspace\Models\Role;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Models\Workspace;

/**
 * @property-read \App\Models\User $this
 */
trait ManagesWorkspaces
{
    /**
     * Get the current workspace of the user's context.
     */
    public function currentWorkspace(): BelongsTo
    {
        if (is_null($this->workspace_id) && $this->id) {
            $this->switchWorkspace($this->fallbackWorkspace());
        }

        return $this->belongsTo(Workspace::class);
    }

    /**
     * Determine if the given workspace is the current workspace.
     */
    public function isCurrentTeam(Workspace $workspace): bool
    {
        return $workspace->id === $this->currentWorkspace->id;
    }

    /**
     * Switch the user's context to the given workspace.
     */
    public function switchWorkspace(Workspace $workspace): bool
    {
        if (! $this->belongsToWorkspace($workspace)) {
            return false;
        }

        $this->forceFill([
            'workspace_id' => $workspace->id,
        ])->save();

        $this->setRelation('currentWorkspace', $workspace);

        return true;
    }

    /**
     * Get all of the workspaces the user owns or belongs to.
     */
    public function allWorkspaces(): Collection
    {
        return $this->ownedWorkspaces->merge($this->workspaces)->sortBy('name');
    }

    /**
     * Get all of the workspaces the user owns.
     */
    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * Get all of the workspaces the user belongs to.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get the user's "fallback" workspace.
     */
    public function fallbackWorkspace(): Workspace
    {
        return $this->ownedWorkspaces->latest('id')->first();
    }

    /**
     * Determine if the user owns the given workspace.
     */
    public function ownsWorkspace(Workspace $workspace): bool
    {
        if (is_null($workspace)) {
            return false;
        }

        return $this->id === $workspace->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given workspace.
     */
    public function belongsToWorkspace(?Workspace $workspace): bool
    {
        if (is_null($workspace)) {
            return false;
        }
        if ($this->ownsWorkspace($workspace)) {
            return true;
        }

        return (bool) $this->workspaces->contains(fn ($t): bool => $t->id === $workspace->id);
    }

    /**
     * Get the role that the user has on the workspace.
     */
    public function teamRole(Workspace $workspace): ?Role
    {
        if ($this->ownsWorkspace($workspace)) {
            return new OwnerRole();
        }

        if (! $this->belongsToWorkspace($workspace)) {
            return null;
        }

        $role = $workspace->users
            ->where('id', $this->id)
            ->first()
            ->membership
            ->role;

        return $role ? RoleRegistry::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given workspace.
     */
    public function hasWorkspaceRole(Workspace $workspace, string $role): bool
    {
        if ($this->ownsWorkspace($workspace)) {
            return true;
        }

        return $this->belongsToWorkspace($workspace) && optional(RoleRegistry::findRole($workspace->users->where(
            'id', $this->id
        )->first()->membership->role))->key === $role;
    }
}
