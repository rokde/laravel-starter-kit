<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Workspace\Actions\InviteMember;
use Modules\Workspace\Http\Requests\StoreInvitationRequest;
use Modules\Workspace\Models\RoleRegistry;

class WorkspaceInvitationsController
{
    public function index(Request $request)
    {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('workspace::invitations/Index', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => $workspace->owner,
            'invitations' => $workspace->invitations->map(fn ($invitation) => $invitation->only('id', 'email', 'role', 'created_at')),
            'roles' => RoleRegistry::$roles,
        ]);
    }

    public function store(
        StoreInvitationRequest $request,
        InviteMember $inviteMember,
    ): RedirectResponse {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $inviteMember->handle($workspace, $request->getEmail(), $request->validated('role'));

        return redirect()
            ->back()
            ->with('message', __('Invitation sent.'));
    }
}
