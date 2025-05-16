<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Workspace\Actions\RemoveMember;
use Modules\Workspace\Actions\UpdateMember;
use Modules\Workspace\Models\RoleRegistry;

class WorkspaceMembersController
{
    public function index(Request $request): Response
    {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('workspace::members/Index', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => $workspace->owner,
            'members' => $workspace->users,
            'roles' => RoleRegistry::$roles,
            'abilities' => [
                'members.create' => $request->user()->can('addMember', $workspace),
                'members.update' => $request->user()->can('updateMember', $workspace),
                'members.remove' => $request->user()->can('removeMember', $workspace),
            ],
        ]);
    }

    public function update(
        Request $request,
        UpdateMember $updateMember,
    ): RedirectResponse {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $updateMember->handle($workspace, new Id($request->integer('id')), (string) $request->string('role'));

        return redirect()
            ->back()
            ->with('message', __('Member updated.'));
    }

    public function destroy(
        Request $request,
        User $member,
        RemoveMember $removeMember,
    ): RedirectResponse {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $removeMember->handle($workspace, $member);

        return redirect()
            ->back()
            ->with('message', __('Member removed.'));
    }
}
