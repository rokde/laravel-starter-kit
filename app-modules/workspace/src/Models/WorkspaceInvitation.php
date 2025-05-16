<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class WorkspaceInvitation extends Model
{
    protected $table = 'workspace_member_invitations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the workspace that the invitation belongs to.
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function generateNameFromEmail(): string
    {
        return Str::of($this->email)
            ->before('@')
            ->before('+')
            ->replace('.', ' ')
            ->title()
            ->toString();
    }

    public function getAcceptUrl(): string
    {
        return URL::signedRoute('public.api.invitations.accept', [
            'invitation' => $this->id,
        ]);
    }
}
