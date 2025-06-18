<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\CustomProperties\Models\Concerns\DefinesCustomProperties;
use Modules\Workspace\Database\Factories\WorkspaceFactory;
use Modules\Workspace\DataTransferObjects\Owner as OwnerDto;
use Modules\Workspace\DataTransferObjects\Workspace as WorkspaceDto;
use Modules\Workspace\Events\WorkspaceCreated;
use Modules\Workspace\Events\WorkspaceDeleted;
use Modules\Workspace\Events\WorkspaceUpdated;

#[UseFactory(WorkspaceFactory::class)]
class Workspace extends Model
{
    use DefinesCustomProperties, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => WorkspaceCreated::class,
        'updated' => WorkspaceUpdated::class,
        'deleted' => WorkspaceDeleted::class,
    ];

    /**
     * Get the owner of the workspace.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the workspace's users including its owner.
     */
    public function allUsers(): Collection
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all of the users that belong to the workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Membership::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    /**
     * Get all of the pending user invitations for the workspace.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(WorkspaceInvitation::class);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     */
    public function hasUserWithEmail(string $email): bool
    {
        return $this->allUsers()->contains(fn ($user): bool => $user->email === $email);
    }

    public function toDto(): WorkspaceDto
    {
        return new WorkspaceDto(
            id: $this->id,
            name: $this->name,
            owner: new OwnerDto(
                id: $this->owner->id,
                name: $this->owner->name,
                email: $this->owner->email,
            ),
            currentWorkspace: false,
        );
    }
}
