<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Modules\Workspace\Actions\AcceptTeamInvitation;
use Modules\Workspace\Actions\InviteMember;
use Modules\Workspace\Http\Requests\StoreInvitationRequest;
use Modules\Workspace\Models\RoleRegistry;
use Modules\Workspace\Models\WorkspaceInvitation;
use Throwable;

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
            'invitations' => $workspace->invitations->map(fn ($invitation) => $invitation->only('id', 'email', 'role',
                'created_at')),
            'roles' => RoleRegistry::$roles,
            'abilities' => [
                'members.create' => $request->user()->can('addMember', $workspace),
            ],
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

    public function acceptInvitation(
        Request $request,
        #[RouteParameter('invitation')]
        string $invitationId,
        AcceptTeamInvitation $acceptTeamInvitation,
    ): RedirectResponse {
        abort_unless($request->hasValidSignature(), Response::HTTP_FORBIDDEN);

        if (! $request->user()) {
            return redirect()->route('login');
        }

        $invitation = WorkspaceInvitation::findOrFail($invitationId);

        // if signed in user is here, then accept the invitation and switch to the workspace
        try {
            $acceptTeamInvitation->handle($invitation, $request->user());
        } catch (Throwable $e) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return redirect()
            ->route('dashboard')
            ->with('message', __('You have been added to the workspace.'));
    }
}
